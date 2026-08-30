<?php
/**
 * =====================================================================
 * MPVS — M-Pesa Payment Verification & Transaction Management System
 * Application configuration (single source of settings)
 * =====================================================================
 * Edit the values below to match your environment (XAMPP defaults work
 * out of the box: host=localhost, user=root, password=empty).
 */

/* ---------------------------------------------------------------------
 * Database (MySQL via PDO — XAMPP defaults)
 * ------------------------------------------------------------------- */
define('DB_HOST', 'localhost');
define('DB_NAME', 'mpesa_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/* ---------------------------------------------------------------------
 * Paths & URLs
 * ------------------------------------------------------------------- */
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH',  ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('PUBLIC_PATH', ROOT_PATH . '/public');

/**
 * Base URL of the application. Leave as '' for automatic detection
 * (works when installed at http://localhost/mpesa-projo/public or with
 * the doc root pointed directly at the /public folder).
 * Set an absolute value here ONLY if auto-detection misbehaves, e.g.
 * define('APP_URL_BASE', 'http://localhost/mpesa-projo/public');
 */
if (!defined('APP_URL_BASE')) {
    define('APP_URL_BASE', '');
}

/* ---------------------------------------------------------------------
 * Application branding (shown on login page, receipts and top bar)
 * ------------------------------------------------------------------- */
define('APP_NAME', 'MPVS');
define('APP_FULL_NAME', 'M-Pesa Payment Verification & Transaction Management System');
define('APP_MODE_LABEL', 'Manual Verification System');
define('APP_MODE_DESCRIPTION', 'This application verifies transactions that have been manually recorded in the local database. No external M-Pesa API is required.');
define('BUSINESS_NAME', 'M-Pesa Verification Services Ltd.');
define('BUSINESS_TAGLINE', 'Fast, secure payment confirmation for small businesses');
define('BUSINESS_PHONE', '+254 700 000 000');
define('BUSINESS_EMAIL', 'support@mpvs.local');
define('BUSINESS_ADDRESS', 'Moi Avenue, Nairobi, Kenya');
define('DEFAULT_CURRENCY', 'KES');

/* ---------------------------------------------------------------------
 * Verification model
 * -------------------------------------------------------------------
 * This is a MANUAL verification system: transactions are recorded in
 * the local database by an administrator and then verified on the
 * Verify Transaction page.
 *
 * When MPESA_SIMULATION_MODE is true (the default), verification
 * produces deterministic simulated results. Set to false and provide
 * real Safaricom credentials below to enable live Daraja API calls.
 * ------------------------------------------------------------------- */
define('MPESA_SIMULATION_MODE', true);
define('MPESA_API_BASE',       'https://sandbox.safaricom.co.ke');
define('MPESA_CONSUMER_KEY',    '');  // Safaricom consumer key
define('MPESA_CONSUMER_SECRET', '');  // Safaricom consumer secret
define('MPESA_SHORTCODE',       '');  // Business shortcode
define('MPESA_PASSKEY',         '');  // Lipa Na M-Pesa passkey
define('MPESA_REQUEST_TIMEOUT', 15);  // cURL timeout in seconds

/* ---------------------------------------------------------------------
 * Security & session settings
 * ------------------------------------------------------------------- */
define('SESSION_NAME', 'mpvs_session');
define('PASSWORD_MIN_LENGTH', 8);
define('AUDIT_LOG_ENABLED', true);

/* ---------------------------------------------------------------------
 * Environment
 * ------------------------------------------------------------------- */
define('DEFAULT_TIMEZONE', 'Africa/Nairobi');
define('DEBUG_MODE', true); // set to false in production
