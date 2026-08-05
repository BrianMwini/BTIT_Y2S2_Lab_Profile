<?php
/**
 * =====================================================================
 * MPVS — User model (SRS: User entity)
 * Administrators & business staff. Staff registrations start as
 * 'pending' and require administrator approval before they can log in.
 * All queries use prepared statements.
 * =====================================================================
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class User
{
    /** Statuses a user account can be in (approval workflow). */
    public const STATUSES = ['pending', 'approved', 'rejected', 'inactive'];

    /** Find a user by primary key. */
    public static function find(int $id): ?array
    {
        return Database::fetchOne('SELECT * FROM users WHERE id = ?', [$id]);
    }

    /** Find a user by username (used for login). */
    public static function findByUsername(string $username): ?array
    {
        return Database::fetchOne('SELECT * FROM users WHERE username = ?', [$username]);
    }

    /** Find a user by email. */
    public static function findByEmail(string $email): ?array
    {
        return Database::fetchOne('SELECT * FROM users WHERE email = ?', [$email]);
    }

    /** Verify username + password. Returns the user array or null. */
    public static function authenticate(string $username, string $password): ?array
    {
        $user = self::findByUsername($username);
        if ($user === null || !password_verify($password, $user['password_hash'])) {
            return null;
        }
        return $user;
    }

    /** List all users, optionally filtered by search term (name/username/email). */
    public static function all(string $search = ''): array
    {
        $sql = 'SELECT * FROM users';
        $params = [];
        if ($search !== '') {
            $sql .= ' WHERE full_name LIKE ? OR username LIKE ? OR email LIKE ? OR phone LIKE ?';
            $like = '%' . $search . '%';
            $params = [$like, $like, $like, $like];
        }
        $sql .= ' ORDER BY created_at DESC, id DESC';
        return Database::fetchAll($sql, $params);
    }

    /** Users awaiting administrator approval (newest first). */
    public static function pending(): array
    {
        return Database::fetchAll(
            "SELECT * FROM users WHERE status = 'pending' ORDER BY created_at ASC, id ASC"
        );
    }

    /** Create a new user. Returns the new id. */
    public static function create(array $data): int
    {
        $sql = 'INSERT INTO users (full_name, username, email, phone, password_hash, role, status)
                VALUES (:full_name, :username, :email, :phone, :password_hash, :role, :status)';
        Database::query($sql, [
            'full_name'     => $data['full_name'],
            'username'      => $data['username'],
            'email'         => $data['email'],
            'phone'         => $data['phone'] ?? null,
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role'          => $data['role'] ?? 'staff',
            'status'        => in_array($data['status'] ?? 'approved', self::STATUSES, true)
                ? $data['status'] : 'approved',
        ]);
        return (int) Database::connection()->lastInsertId();
    }

    /** Update a user profile. Password is optional (only re-hashed when provided). */
    public static function update(int $id, array $data): void
    {
        $sql = 'UPDATE users SET full_name = :full_name, username = :username, email = :email,
                phone = :phone, role = :role, status = :status';
        $params = [
            'id'        => $id,
            'full_name' => $data['full_name'],
            'username'  => $data['username'],
            'email'     => $data['email'],
            'phone'     => $data['phone'] ?? null,
            'role'      => $data['role'] ?? 'staff',
            'status'    => in_array($data['status'] ?? 'approved', self::STATUSES, true)
                ? $data['status'] : 'approved',
        ];
        if (!empty($data['password'])) {
            $sql .= ', password_hash = :password_hash';
            $params['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        $sql .= ' WHERE id = :id';
        Database::query($sql, $params);
    }

    /**
     * Set a user's status (approve / reject / suspend / activate).
     * Returns the new status, or null when the user does not exist.
     */
    public static function setStatus(int $id, string $status): ?string
    {
        if (!in_array($status, self::STATUSES, true)) {
            return null;
        }
        $user = self::find($id);
        if ($user === null) {
            return null;
        }
        Database::query('UPDATE users SET status = ? WHERE id = ?', [$status, $id]);
        return $status;
    }

    /** Record the last-login timestamp. */
    public static function markLogin(int $id): void
    {
        Database::query('UPDATE users SET last_login_at = NOW() WHERE id = ?', [$id]);
    }

    /** Count users by role (all statuses). */
    public static function countByRole(string $role): int
    {
        $row = Database::fetchOne('SELECT COUNT(*) AS c FROM users WHERE role = ?', [$role]);
        return (int) ($row['c'] ?? 0);
    }

    /** Count users by role who are currently approved (can log in). */
    public static function countApprovedByRole(string $role): int
    {
        $row = Database::fetchOne('SELECT COUNT(*) AS c FROM users WHERE role = ? AND status = ?', [$role, 'approved']);
        return (int) ($row['c'] ?? 0);
    }

    /** Count approved users (accounts that may log in). */
    public static function countApproved(): int
    {
        $row = Database::fetchOne("SELECT COUNT(*) AS c FROM users WHERE status = 'approved'");
        return (int) ($row['c'] ?? 0);
    }

    /** Count users awaiting administrator approval. */
    public static function countPending(): int
    {
        $row = Database::fetchOne("SELECT COUNT(*) AS c FROM users WHERE status = 'pending'");
        return (int) ($row['c'] ?? 0);
    }
}
