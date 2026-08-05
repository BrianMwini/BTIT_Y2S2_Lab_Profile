<?php
/**
 * =====================================================================
 * MPVS — User management controller (SRS 4.2.7 User Management)
 * Administrator-only. Includes the staff approval workflow: pending
 * registrations can be approved or rejected; active accounts can be
 * suspended and re-activated. "The module ensures that only authorized
 * personnel can access system resources and perform critical operations."
 * =====================================================================
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\AuditLog;
use App\Models\User;

class UserController extends Controller
{
    /** GET /users — list all accounts with search + pending approvals. */
    public function index(array $params = []): void
    {
        Auth::requireRole('admin');
        $search = $this->input('q');
        $this->render('users/index', [
            'title'   => 'User Management',
            'user'    => Auth::user(),
            'users'   => User::all($search),
            'pending' => User::pending(),
            'search'  => $search,
        ]);
    }

    /** GET /users/create — new user form. */
    public function create(array $params = []): void
    {
        Auth::requireRole('admin');
        $this->render('users/form', ['title' => 'New User', 'user' => Auth::user(), 'editing' => null]);
    }

    /** POST /users/store — persist a new account. */
    public function store(array $params = []): void
    {
        Auth::requireRole('admin');
        $this->verifyCsrf();

        $data = [
            'full_name' => $this->post('full_name'),
            'username'  => $this->post('username'),
            'email'     => $this->post('email'),
            'phone'     => $this->post('phone'),
            'role'      => $this->post('role', 'staff'),
            'status'    => $this->post('status', 'approved'),
            'password'  => $this->post('password'),
        ];

        $errors = $this->validate($data, true);

        if (empty($errors)) {
            $userId = User::create($data);
            AuditLog::log(Auth::id(), 'user_created', 'Created account ' . $data['username'] . ' (' . $data['role'] . ', ' . $data['status'] . ')');
            Flash::set('success', 'User "' . $data['full_name'] . '" created successfully.');
            redirect('users');
        }

        remember_inputs($data);
        Flash::set('danger', 'Please correct the errors below.');
        $this->render('users/form', [
            'title' => 'New User',
            'user' => Auth::user(),
            'editing' => null,
            'errors' => $errors,
            'old' => $_SESSION['old_input'] ?? [],
        ]);
    }

    /** GET /users/edit/{id} — edit form. */
    public function edit(array $params = []): void
    {
        Auth::requireRole('admin');
        $user = User::find((int) ($params['id'] ?? 0));
        if ($user === null) {
            $this->render('errors/404', [], 'none');
            return;
        }
        $this->render('users/form', ['title' => 'Edit User', 'user' => Auth::user(), 'editing' => $user]);
    }

    /** POST /users/update — save profile changes. */
    public function update(array $params = []): void
    {
        Auth::requireRole('admin');
        $this->verifyCsrf();

        $id = (int) $this->post('id');
        $editing = User::find($id);
        if ($editing === null) {
            $this->render('errors/404', [], 'none');
            return;
        }

        $data = [
            'full_name' => $this->post('full_name'),
            'username'  => $this->post('username'),
            'email'     => $this->post('email'),
            'phone'     => $this->post('phone'),
            'role'      => $this->post('role', 'staff'),
            'status'    => $this->post('status', 'approved'),
            'password'  => $this->post('password'),
        ];

        $errors = $this->validate($data, false, $id);

        // Guard: the last approved admin cannot demote or suspend themselves.
        $activeAdmins = User::countApprovedByRole('admin');
        if ($activeAdmins <= 1 && $editing['role'] === 'admin' && ($data['role'] !== 'admin' || $data['status'] !== 'approved')) {
            $errors['role'] = 'You are the only approved administrator. Demote or suspend another admin first.';
        }

        if (empty($errors)) {
            User::update($id, $data);
            AuditLog::log(Auth::id(), 'user_updated', 'Updated account ' . $data['username']);
            Flash::set('success', 'User "' . $data['full_name'] . '" updated successfully.');
            redirect('users');
        }

        remember_inputs($data);
        Flash::set('danger', 'Please correct the errors below.');
        $editing = User::find($id); // re-read to keep original values in form
        $this->render('users/form', [
            'title'   => 'Edit User',
            'user'    => Auth::user(),
            'editing' => $editing,
            'errors'  => $errors,
            'old'     => $_SESSION['old_input'] ?? [],
        ]);
    }

    /**
     * POST /users/status — approve, reject, suspend or activate a user.
     * The new status is posted as 'status' (approved|rejected|inactive).
     */
    public function setStatus(array $params = []): void
    {
        Auth::requireRole('admin');
        $this->verifyCsrf();

        $id = (int) $this->post('id');
        $status = $this->post('status');
        $target = User::find($id);

        if ($target === null) {
            Flash::set('danger', 'User not found.');
            redirect('users');
        }
        if (!in_array($status, ['approved', 'rejected', 'inactive'], true)) {
            Flash::set('danger', 'Invalid status action.');
            redirect('users');
        }
        // Self-action guard: you cannot approve/reject/suspend yourself.
        if ($id === Auth::id()) {
            Flash::set('danger', 'You cannot change the status of your own account.');
            redirect('users');
        }
        // Last-admin guard: never remove the only approved administrator.
        if ($target['role'] === 'admin' && $target['status'] === 'approved'
            && $status !== 'approved' && User::countApprovedByRole('admin') <= 1) {
            Flash::set('danger', 'You cannot suspend or reject the only approved administrator.');
            redirect('users');
        }

        User::setStatus($id, $status);

        // Pick a precise audit action + human message.
        $action = match (true) {
            $status === 'approved' && $target['status'] === 'pending'  => 'user_approved',
            $status === 'approved'                                      => 'user_activated',
            $status === 'rejected'                                      => 'user_rejected',
            default                                                     => 'user_suspended',
        };
        $pastTense = match ($action) {
            'user_approved'  => 'approved',
            'user_rejected'  => 'rejected',
            'user_suspended' => 'suspended',
            default          => 'activated',
        };
        AuditLog::log(Auth::id(), $action, $target['username'] . ' was ' . $pastTense . ' (' . $status . ')');
        Flash::set('success', 'User "' . $target['full_name'] . '" has been ' . $pastTense . '.');
        redirect('users');
    }

    /** Shared validation for create/update forms. */
    private function validate(array $data, bool $requirePassword, int $ignoreId = 0): array
    {
        $errors = [];
        if ($data['full_name'] === '' || strlen($data['full_name']) < 3) {
            $errors['full_name'] = 'Full name must be at least 3 characters.';
        }
        if (!preg_match('/^[a-zA-Z0-9_.]{3,30}$/', $data['username'])) {
            $errors['username'] = 'Username must be 3–30 characters (letters, numbers, underscore, dot).';
        } else {
            $existing = User::findByUsername($data['username']);
            if ($existing !== null && (int) $existing['id'] !== $ignoreId) {
                $errors['username'] = 'This username is already taken.';
            }
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'A valid email address is required.';
        } else {
            $existing = User::findByEmail($data['email']);
            if ($existing !== null && (int) $existing['id'] !== $ignoreId) {
                $errors['email'] = 'This email is already registered.';
            }
        }
        if (!in_array($data['role'], ['admin', 'staff'], true)) {
            $errors['role'] = 'Invalid role selected.';
        }
        if (!in_array($data['status'], User::STATUSES, true)) {
            $errors['status'] = 'Invalid status selected.';
        }
        if ($requirePassword && !preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $data['password'])) {
            $errors['password'] = 'Password must be at least 8 characters and include letters and numbers.';
        } elseif (!$requirePassword && $data['password'] !== '' && !preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $data['password'])) {
            $errors['password'] = 'Password must be at least 8 characters and include letters and numbers.';
        }
        return $errors;
    }
}
