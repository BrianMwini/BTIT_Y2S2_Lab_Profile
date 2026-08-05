<?php
/**
 * =====================================================================
 * MPVS — CSRF protection
 * Every POST form embeds a per-session token that is verified server side.
 * =====================================================================
 */

declare(strict_types=1);

namespace App\Core;

class Csrf
{
    /** Return the session token, generating one if absent. */
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /** Output the hidden input field (see helper csrf_field()). */
    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . e(self::token()) . '">';
    }

    /** Verify the submitted token; abort with 419 when invalid. */
    public static function verify(): void
    {
        $submitted = $_POST['csrf_token'] ?? '';
        if (!is_string($submitted) || $submitted === '' || !hash_equals(self::token(), $submitted)) {
            http_response_code(419);
            die('Session expired or invalid security token. Please go back, reload the page and try again.');
        }
    }
}
