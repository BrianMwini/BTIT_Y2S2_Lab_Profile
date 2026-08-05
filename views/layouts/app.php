<?php
/**
 * Main dashboard layout: sidebar + topbar + content area.
 * Reused by every authenticated page. Variables available:
 *   $user (current user), $content (inner view), $title (optional)
 */
$currentPath = trim($_SERVER['REQUEST_URI'] ?? '', '/');
$currentPath = preg_replace('/\?.*$/', '', $currentPath);
$isActive = function (string $route) use ($currentPath): bool {
    return $currentPath === $route || ($route !== '' && str_starts_with($currentPath, $route . '/'));
};
$pageTitle = $title ?? APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — <?= e(APP_NAME) ?></title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💳</text></svg>">
    <link rel="stylesheet" href="<?= url('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/vendor/fontawesome/all.min.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/app.css') ?>">
</head>
<body>

<div class="app-wrapper">
    <!-- ============ SIDEBAR ============ -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="fa-solid fa-mobile-screen-button"></i></div>
            <div class="brand-text">
                <strong><?= e(APP_NAME) ?></strong>
                <small>Payment Verification</small>
            </div>
        </div>

        <nav class="sidebar-nav">
            <p class="nav-section-label">Main Menu</p>
            <a href="<?= url('') ?>" class="nav-link <?= $isActive('') ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge-high"></i><span>Dashboard</span>
            </a>
            <a href="<?= url('verify') ?>" class="nav-link <?= $isActive('verify') ? 'active' : '' ?>">
                <i class="fa-solid fa-shield-halved"></i><span>Verify Transaction</span>
                <?php if (!empty($pendingVerifications ?? 0)): ?>
                    <span class="badge rounded-pill bg-warning ms-auto"><?= (int) $pendingVerifications ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= url('transactions') ?>" class="nav-link <?= $isActive('transactions') ? 'active' : '' ?>">
                <i class="fa-solid fa-arrow-right-arrow-left"></i><span>Transactions</span>
            </a>

            <?php if ($user['role'] === 'admin'): ?>
                <a href="<?= url('transactions/create') ?>" class="nav-link <?= $isActive('transactions/create') ? 'active' : '' ?>">
                    <i class="fa-solid fa-plus"></i><span>Add Transaction</span>
                </a>
                <p class="nav-section-label mt-4">Management</p>
                <a href="<?= url('reports') ?>" class="nav-link <?= $isActive('reports') ? 'active' : '' ?>">
                    <i class="fa-solid fa-chart-pie"></i><span>Reports</span>
                </a>
                <a href="<?= url('users') ?>" class="nav-link <?= $isActive('users') ? 'active' : '' ?>">
                    <i class="fa-solid fa-users-gear"></i><span>User Management</span>
                    <?php if (!empty($pendingApprovals ?? 0)): ?>
                        <span class="badge rounded-pill bg-danger ms-auto"><?= (int) $pendingApprovals ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <?= role_badge($user['role']) ?>
            <a href="<?= url('logout') ?>" class="btn btn-sm btn-outline-light w-100 mt-3">
                <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Sidebar backdrop (mobile) -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- ============ MAIN AREA ============ -->
    <main class="main-area">
        <!-- Topbar -->
        <header class="topbar">
            <button class="btn btn-icon d-lg-none" id="sidebarToggle" aria-label="Toggle menu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <h1 class="topbar-title"><?= e($pageTitle) ?></h1>

            <div class="topbar-right">
                <span class="badge manual-badge" title="<?= e(APP_MODE_DESCRIPTION) ?>">
                    <i class="fa-solid fa-shield-halved me-1"></i><?= e(APP_MODE_LABEL) ?>
                </span>

                <!-- Notifications -->
                <div class="dropdown">
                    <button class="btn btn-icon position-relative" data-bs-toggle="dropdown" aria-label="Notifications">
                        <i class="fa-regular fa-bell"></i>
                        <?php if (!empty($notifications ?? [])): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= count($notifications) ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notification-menu">
                        <div class="notification-header">
                            <strong>Recent activity</strong>
                            <span class="text-muted small"><?= count($notifications ?? []) ?> items</span>
                        </div>
                        <?php if (empty($notifications ?? [])): ?>
                            <div class="notification-empty"><i class="fa-regular fa-bell-slash"></i><span>No recent activity</span></div>
                        <?php else: ?>
                            <?php foreach ($notifications as $note): ?>
                                <div class="notification-item">
                                    <div class="notification-icon">
                                        <?php
                                        $icon = match ($note['action']) {
                                            'login' => 'fa-right-to-bracket text-primary',
                                            'login_failed' => 'fa-triangle-exclamation text-danger',
                                            'verify_transaction' => 'fa-shield-halved text-success',
                                            'transaction_created' => 'fa-plus text-primary',
                                            'transaction_failed' => 'fa-circle-xmark text-danger',
                                            'generate_receipt' => 'fa-receipt text-info',
                                            'generate_report' => 'fa-chart-pie text-warning',
                                            'register' => 'fa-user-clock text-warning',
                                            'user_approved' => 'fa-user-check text-success',
                                            'user_rejected' => 'fa-user-xmark text-danger',
                                            'user_suspended' => 'fa-user-slash text-secondary',
                                            'user_activated' => 'fa-user-check text-success',
                                            'user_created', 'user_updated' => 'fa-user-gear text-secondary',
                                            default => 'fa-circle-info text-muted',
                                        };
                                        ?>
                                        <i class="fa-solid <?= $icon ?>"></i>
                                    </div>
                                    <div class="notification-body">
                                        <span class="small"><?= e(audit_action_label($note['action'])) ?></span>
                                        <span class="small text-muted d-block text-truncate" style="max-width: 220px;"><?= e($note['details'] ?? '') ?></span>
                                    </div>
                                    <span class="notification-time"><?= date('H:i', strtotime($note['created_at'])) ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- User -->
                <div class="dropdown">
                    <button class="btn user-chip" data-bs-toggle="dropdown" aria-label="Account menu">
                        <span class="user-avatar"><?= e(strtoupper(mb_substr($user['full_name'], 0, 1))) ?></span>
                        <span class="d-none d-md-inline user-chip-text">
                            <strong><?= e($user['full_name']) ?></strong>
                            <small><?= $user['role'] === 'admin' ? 'Administrator' : 'Business Staff' ?></small>
                        </span>
                        <i class="fa-solid fa-chevron-down fa-xs text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><span class="dropdown-item-text small text-muted">Signed in as <strong><?= e($user['username']) ?></strong></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= url('logout') ?>"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Flash messages -->
        <?php require VIEWS_PATH . '/partials/flash.php'; ?>

        <!-- Page content -->
        <div class="page-content">
            <?= $content ?>
        </div>

        <footer class="app-footer">
            <span><?= e(APP_FULL_NAME) ?></span>
            <span class="text-muted">© <?= date('Y') ?> — <?= e(BUSINESS_NAME) ?></span>
        </footer>
    </main>
</div>

<script src="<?= url('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= url('assets/vendor/chartjs/chart.umd.min.js') ?>"></script>
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html>
