<?php
/**
 * =====================================================================
 * MPVS — Application bootstrap
 * Loads configuration, starts a hardened session, registers the class
 * autoloader and defines global helpers.
 * =====================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

/* ---------------------------------------------------------------------
 * Error reporting (DEBUG_MODE toggles verbosity)
 * ------------------------------------------------------------------- */
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    ini_set('display_errors', '0');
}

/* ---------------------------------------------------------------------
 * Session (hardened) — must start before any output
 * ------------------------------------------------------------------- */
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    ]);
    session_start();
}

/* ---------------------------------------------------------------------
 * Timezone
 * ------------------------------------------------------------------- */
date_default_timezone_set(DEFAULT_TIMEZONE);

/* ---------------------------------------------------------------------
 * PSR-4 style autoloader (no Composer required):
 *   App\Core\X        -> app/core/X.php
 *   App\Models\X      -> app/models/X.php
 *   App\Controllers\X -> app/controllers/X.php
 *   App\Services\X    -> app/services/X.php
 * ------------------------------------------------------------------- */
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $map = [
        'Core\\'       => '/core/',
        'Models\\'     => '/models/',
        'Controllers\\' => '/controllers/',
        'Services\\'   => '/services/',
    ];
    foreach ($map as $ns => $dir) {
        if (strncmp($relative, $ns, strlen($ns)) === 0) {
            $file = APP_PATH . $dir . str_replace('\\', '/', substr($relative, strlen($ns))) . '.php';
            if (is_file($file)) {
                require_once $file;
            }
            return;
        }
    }
});

/* ---------------------------------------------------------------------
 * Base URL auto-detection (only when not manually configured)
 * ------------------------------------------------------------------- */
if (APP_URL_BASE === '') {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $scriptDir = ($scriptDir === '.' || $scriptDir === '/') ? '' : $scriptDir;
    define('BASE_URL', rtrim($scriptDir, '/'));
} else {
    define('BASE_URL', rtrim(APP_URL_BASE, '/'));
}

require_once APP_PATH . '/core/Helpers.php';
