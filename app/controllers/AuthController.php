<?php
/**
 * =====================================================================
 * MPVS — Authentication controller (SRS FR-01: registration & login)
 * ---------------------------------------------------------------------
 * Staff registrations start as 'pending' and require administrator
 * approval before they can log in. Approved accounts log in normally;
 * pending / rejected / inactive accounts are blocked with a clear message.
 * =====================================================================
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\AuditLog;
use App\Models\User;

class AuthController extends Controller
{
    /** GET /login — show the login form. */
    public function login(array $params = []): void
    {
        Auth::requireGuest();
        $this->render('auth/login', ['title' => 'Sign In'], 'auth');
    }

    /** POST /login — authenticate credentials. */
    public function doLogin(array $params = []): void
    {
        Auth::requireGuest();
        $this->verifyCsrf();

        $username = $this->post('username');
        $password = $this->post('password');

        // Basic field validation.
        $errors = [];
        if ($username === '') {
            $errors['username'] = 'Username is required.';
        }
        if ($password === '') {
            $errors['password'] = 'Password is required.';
        }

        if (empty($errors)) {
            $user = User::authenticate($username, $password);
            if ($user !== null && $user['status'] === 'approved') {
                Auth::login((int) $user['id']);
                User::markLogin((int) $user['id']);
                AuditLog::log((int) $user['id'], 'login', $user['username'] . ' logged in');
                Flash::set('success', 'Welcome back, ' . $user['full_name'] . '!');
                redirect('');
            }
            if ($user !== null) {
                // Credentials matched but the account is not approved yet.
                $message = match ($user['status']) {
                    'pending'  => 'Your account is awaiting administrator approval. You will be able to log in once it is approved.',
                    'rejected' => 'Your registration was rejected. Contact the administrator for assistance.',
                    default    => 'Your account is inactive (suspended). Contact the administrator.',
                };
                AuditLog::log((int) $user['id'], 'login_failed', 'Blocked login (' . $user['status'] . ' account): ' . $username);
                $errors['auth'] = $message;
            } else {
                AuditLog::log(null, 'login_failed', 'Invalid credentials for username: ' . $username);
                $errors['auth'] = 'Invalid username or password.';
            }
        }

        remember_inputs(['username' => $username]);
        Flash::set('danger', 'You could not be signed in. Please check the message below.');
        $this->render('auth/login', ['title' => 'Sign In', 'errors' => $errors, 'old' => $_SESSION['old_input'] ?? []], 'auth');
    }

    /** GET /register — show the registration form (SRS FR-01). */
    public function register(array $params = []): void
    {
        Auth::requireGuest();
        $this->render('auth/register', ['title' => 'Create Account'], 'auth');
    }

    /** POST /register — create a new staff account (pending approval). */
    public function doRegister(array $params = []): void
    {
        Auth::requireGuest();
        $this->verifyCsrf();

        $data = [
            'full_name' => $this->post('full_name'),
            'username'  => $this->post('username'),
            'email'     => $this->post('email'),
            'phone'     => $this->post('phone'),
            'password'  => $this->post('password'),
            'password2' => $this->post('password2'),
        ];

        // Validation (SRS gap #4: password rules defined during design).
        $errors = [];
        if ($data['full_name'] === '' || strlen($data['full_name']) < 3) {
            $errors['full_name'] = 'Full name must be at least 3 characters.';
        }
        if (!preg_match('/^[a-zA-Z0-9_.]{3,30}$/', $data['username'])) {
            $errors['username'] = 'Username must be 3–30 characters (letters, numbers, underscore, dot).';
        } elseif (User::findByUsername($data['username']) !== null) {
            $errors['username'] = 'This username is already taken.';
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'A valid email address is required.';
        } elseif (User::findByEmail($data['email']) !== null) {
            $errors['email'] = 'This email is already registered.';
        }
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $data['password'])) {
            $errors['password'] = 'Password must be at least 8 characters and include letters and numbers.';
        } elseif ($data['password'] !== $data['password2']) {
            $errors['password2'] = 'Passwords do not match.';
        }

        if (empty($errors)) {
            // New staff accounts always start as PENDING APPROVAL.
            $userId = User::create([
                'full_name' => $data['full_name'],
                'username'  => $data['username'],
                'email'     => $data['email'],
                'phone'     => $data['phone'] !== '' ? $data['phone'] : null,
                'password'  => $data['password'],
                'role'      => 'staff',
                'status'    => 'pending',
            ]);
            AuditLog::log($userId, 'register', 'New staff account created: ' . $data['username'] . ' (pending approval)');
            Flash::set('success', 'Account created successfully. Your account is awaiting administrator approval — you will be able to log in once an administrator approves it.');
            redirect('login');
        }

        remember_inputs($data);
        $this->render('auth/register', ['title' => 'Create Account', 'errors' => $errors, 'old' => $_SESSION['old_input'] ?? []], 'auth');
    }

    /** GET /logout — destroy the session. */
    public function logout(array $params = []): void
    {
        AuditLog::log(Auth::id(), 'logout', 'User signed out');
        Auth::logout();
        Flash::set('info', 'You have been logged out. See you soon!');
        redirect('login');
    }
}
