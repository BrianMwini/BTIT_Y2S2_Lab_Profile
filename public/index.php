<?php
/**
 * =====================================================================
 * MPVS — Front controller (webroot entry point)
 * Loads the application, registers all routes and dispatches the request.
 *
 * Paths arrive in $_GET['r'] — either from the .htaccess rewrite rule
 * (pretty URLs) or from public/router.php (PHP built-in dev server).
 * =====================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Router;

$router = new Router();

/* ------------------------- Public routes -------------------------- */
$router->get('login', 'AuthController@login');
$router->post('login', 'AuthController@doLogin');
$router->get('register', 'AuthController@register');
$router->post('register', 'AuthController@doRegister');
$router->get('logout', 'AuthController@logout');

/* ------------------------- Authenticated routes -------------------- */
$router->get('', 'DashboardController@index');

$router->get('verify', 'TransactionController@verifyForm');

$router->get('transactions', 'TransactionController@index');
$router->get('transactions/create', 'TransactionController@createForm');
$router->post('transactions/store', 'TransactionController@store');
$router->post('transactions/verify', 'TransactionController@markVerified');
$router->post('transactions/fail', 'TransactionController@markFailed');
$router->get('transactions/show/{id}', 'TransactionController@show');
$router->get('receipt/{id}', 'TransactionController@receipt');

/* ------------------------- Admin-only routes ----------------------- */
$router->get('reports', 'ReportController@index');
$router->post('reports/generate', 'ReportController@generate');
$router->get('reports/export', 'ReportController@export');

$router->get('users', 'UserController@index');
$router->get('users/create', 'UserController@create');
$router->post('users/store', 'UserController@store');
$router->get('users/edit/{id}', 'UserController@edit');
$router->post('users/update', 'UserController@update');
$router->post('users/status', 'UserController@setStatus');

/* ------------------------- Dispatch --------------------------------- */
$path = trim((string) ($_GET['r'] ?? ''), '/');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$router->dispatch($method, $path);
