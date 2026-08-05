<?php
/**
 * =====================================================================
 * MPVS — Dev server router (for `php -S localhost:8000 -t public`)
 * Serves real files directly and routes everything else to the front
 * controller, replicating the .htaccess rewrite behaviour.
 * =====================================================================
 */

declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Never resolve paths that traverse outside the webroot.
if (str_contains($path, '..')) {
    http_response_code(404);
    echo 'Not Found';
    return true;
}

// Serve existing files (assets) directly.
if ($path !== '/' && file_exists(__DIR__ . $path) && !is_dir(__DIR__ . $path)) {
    return false;
}

// Map the URL path to the ?r= route parameter.
$_GET['r'] = ltrim($path, '/');
require __DIR__ . '/index.php';
