<?php
/**
 * =====================================================================
 * MPVS — Session authentication & role-based authorization
 * =====================================================================
 */

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

class Auth
{
    /** Is a user currently logged in? */
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /** Current logged-in user id (int) or null. */
    public static function id(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    /** Load the current user record from the database (cached per request). */
    public static function user(): ?array
    {
        $id = self::id();
        if ($id === null) {
            return null;
        }
        static $cache = [];
        if (!isset($cache[$id])) {
            $cache[$id] = User::find($id);
        }
        return $cache[$id];
    }

    /** Is the current user an administrator? */
    public static function isAdmin(): bool
    {
        $user = self::user();
        return $user !== null && $user['role'] === 'admin';
    }

    /**
     * Log a user in after successful credentials check.
     * Regenerates the session id to prevent session fixation.
     */
    public static function login(int $userId): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
    }

    /** Destroy the session (logout). */
    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    /** Guard: redirect guests to the login page. Call at the top of protected actions. */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            redirect('login');
        }
        // Hard guard: a logged-in user whose account is no longer approved
        // (suspended/rejected after login) is signed out immediately.
        $user = self::user();
        if ($user !== null && $user['status'] !== 'approved') {
            self::logout();
            redirect('login');
        }
    }

    /** Guard: block logged-in users from visiting guest pages (login/register). */
    public static function requireGuest(): void
    {
        if (self::check()) {
            redirect('');
        }
    }

    /** Guard: admin-only pages (reports, user management, add/verify actions). */
    public static function requireRole(string $role): void
    {
        self::requireLogin();
        $user = self::user();
        // Only approved accounts are allowed to act (pending/rejected/inactive
        // accounts are blocked server-side even if a session was left behind).
        if ($user === null || $user['status'] !== 'approved' || $user['role'] !== $role) {
            // The SRS constraint "unauthorized access attempts may occur
            // and require monitoring" — every blocked attempt is audited.
            \App\Models\AuditLog::log(
                self::id(),
                'forbidden',
                'Blocked access to ' . ($_SERVER['REQUEST_METHOD'] ?? 'GET') . ' ' . ($_SERVER['REQUEST_URI'] ?? '')
            );
            http_response_code(403);
            require VIEWS_PATH . '/errors/403.php';
            exit;
        }
    }
}
