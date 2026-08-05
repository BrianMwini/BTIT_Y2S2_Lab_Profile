<?php
/**
 * =====================================================================
 * MPVS — One-time web installer
 * ---------------------------------------------------------------------
 * Creates the database, all tables and sample data by executing
 * database/mpesa_db.sql. Visit /setup.php once, then DELETE this file.
 *
 * Security note: if the database already contains the users table, the
 * installer refuses to run again to avoid destroying your data.
 * =====================================================================
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

// Single source of truth: reuse the application configuration so the
// installer and the app can never disagree about DB credentials.
require_once __DIR__ . '/config/config.php';

$messages = [];
$ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['confirm'] ?? '') === 'install') {
    $sqlFile = __DIR__ . '/database/mpesa_db.sql';

    try {
        // Connect WITHOUT the database first (the SQL file creates it).
        $pdo = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        // Safety: refuse to re-install over existing data.
        $exists = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = " . $pdo->quote(DB_NAME))->fetchColumn();
        if ($exists) {
            $hasUsers = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = " . $pdo->quote(DB_NAME) . " AND table_name = 'users'")->fetchColumn();
            if ($hasUsers) {
                $messages[] = ['danger', 'Database "' . DB_NAME . '" already contains tables. Re-installing would destroy data — the installer has been stopped. Delete this setup.php file.'];

                // Fall through: still test the existing connection below.
                $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS);
                $row = $pdo->query('SELECT COUNT(*) AS c FROM users')->fetch(PDO::FETCH_ASSOC);
                $messages[] = ['success', 'Existing installation is healthy: ' . $row['c'] . ' user(s) present.'];
            } else {
                // Database exists but is empty — safe to (re)create schema.
                $pdo->exec('DROP DATABASE `' . DB_NAME . '`');
                $messages[] = ['info', 'Existing empty database removed.'];
                $messages = array_merge($messages, runSchema($pdo, $sqlFile));
            }
        } else {
            $messages = array_merge($messages, runSchema($pdo, $sqlFile));
        }
    } catch (Throwable $e) {
        $messages[] = ['danger', 'Installation failed: ' . htmlspecialchars($e->getMessage())];
    }
}

function runSchema(PDO $pdo, string $sqlFile): array
{
    $sql = file_get_contents($sqlFile);
    if ($sql === false) {
        return [['danger', 'Could not read ' . basename($sqlFile) . '.']];
    }
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    $count = 0;
    foreach ($statements as $statement) {
        if ($statement === '') {
            continue;
        }
        $pdo->exec($statement);
        $count++;
    }
    $count++;
    return [['success', 'Schema created successfully: ' . $count . ' SQL statements executed.']];
}

$installed = false;
try {
    $test = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS);
    $users = (int) $test->query('SELECT COUNT(*) AS c FROM users')->fetchColumn();
    $transactions = (int) $test->query('SELECT COUNT(*) AS c FROM transactions')->fetchColumn();
    $installed = true;
} catch (Throwable $e) {
    $users = $transactions = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MPVS Installer</title>
    <link rel="stylesheet" href="public/assets/vendor/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="public/assets/vendor/fontawesome/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #0b2e24, #0f7a4d); min-height: 100vh; display: grid; place-items: center; padding: 2rem 1rem; }
        .installer { background: #fff; border-radius: 20px; padding: 2.2rem; max-width: 560px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .logo { width: 60px; height: 60px; border-radius: 16px; background: #0f7a4d; color: #fff; display: grid; place-items: center; font-size: 1.5rem; margin-bottom: 1rem; }
        code { background: #f1f5f9; padding: 0.1rem 0.35rem; border-radius: 5px; }
    </style>
</head>
<body>
<div class="installer">
    <div class="logo"><i class="fa-solid fa-mobile-screen-button"></i></div>
    <h3 class="fw-bold mb-1">MPVS Installer</h3>
    <p class="text-muted">M-Pesa Payment Verification & Transaction Management System</p>

    <?php foreach ($messages as [$type, $text]): ?>
        <div class="alert alert-<?= $type ?> py-2 small"><?= $text ?></div>
    <?php endforeach; ?>

    <?php if ($installed): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check me-2"></i><strong>Installation is ready!</strong>
            <ul class="mb-2 mt-2 small">
                <li><strong><?= $users ?></strong> user(s) · <strong><?= $transactions ?></strong> transaction(s) in <code><?= DB_NAME ?></code></li>
            </ul>
        </div>
        <table class="table table-sm small mb-3">
            <tr><td>Administrator login</td><td><code>admin</code> / <code>Admin@123</code></td></tr>
            <tr><td>Staff login</td><td><code>staff1</code> / <code>Staff@123</code></td></tr>
            <tr><td>Pending approval (demo)</td><td><code>newstaff</code> / <code>Staff@123</code></td></tr>
        </table>
        <div class="alert alert-info small py-2 mb-3">
            <i class="fa-solid fa-user-clock me-1"></i>
            New staff registrations require <strong>administrator approval</strong> before they can log in.
        </div>
        <a href="public/" class="btn btn-primary w-100"><i class="fa-solid fa-right-to-bracket me-2"></i>Open the System</a>
        <div class="alert alert-warning small mt-3 mb-0">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            <strong>Delete <code>setup.php</code></strong> from the server before going live.
        </div>
    <?php else: ?>
        <div class="alert alert-info small"><i class="fa-solid fa-circle-info me-2"></i>This will create the <code><?= DB_NAME ?></code> database with all tables and sample data.</div>
        <form method="post">
            <button type="submit" name="confirm" value="install" class="btn btn-primary w-100 btn-lg">
                <i class="fa-solid fa-database me-2"></i>Install Database
            </button>
        </form>
        <p class="text-muted small text-center mt-3 mb-0">Ensure MySQL is running in XAMPP (Apache & MySQL started).</p>
    <?php endif; ?>
</div>
</body>
</html>
