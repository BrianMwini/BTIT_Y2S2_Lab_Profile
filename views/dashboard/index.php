<?php
/**
 * Dashboard view (SRS 4.2.2 Dashboard Interface).
 * Provides an overview of transaction statistics, recent transaction
 * activities and quick access to the major system modules.
 */
?>
<!-- Notification cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <a href="<?= url('verify') ?>" class="text-decoration-none">
            <div class="notify-card border-warning-subtle">
                <div class="notify-icon bg-warning-subtle text-warning"><i class="fa-solid fa-clock"></i></div>
                <div class="notify-body">
                    <span class="notify-label">Pending Verifications</span>
                    <span class="notify-value"><?= $pendingVerifications ?></span>
                </div>
                <?php if ($pendingVerifications > 0): ?><span class="notify-arrow text-warning"><i class="fa-solid fa-arrow-right"></i></span><?php endif; ?>
            </div>
        </a>
    </div>
    <?php if ($user['role'] === 'admin'): ?>
    <div class="col-sm-6 col-xl-3">
        <a href="<?= url('users') ?>" class="text-decoration-none">
            <div class="notify-card <?= $pendingApprovals > 0 ? 'border-danger-subtle' : '' ?>">
                <div class="notify-icon <?= $pendingApprovals > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' ?>"><i class="fa-solid fa-user-clock"></i></div>
                <div class="notify-body">
                    <span class="notify-label">Pending Staff Approvals</span>
                    <span class="notify-value"><?= $pendingApprovals ?></span>
                </div>
                <?php if ($pendingApprovals > 0): ?><span class="notify-arrow text-danger"><i class="fa-solid fa-arrow-right"></i></span><?php endif; ?>
            </div>
        </a>
    </div>
    <?php endif; ?>
    <div class="col-sm-6 col-xl-3">
        <a href="<?= url('transactions?status=verified') ?>" class="text-decoration-none">
            <div class="notify-card">
                <div class="notify-icon bg-success-subtle text-success"><i class="fa-solid fa-circle-check"></i></div>
                <div class="notify-body">
                    <span class="notify-label">Verified Today</span>
                    <span class="notify-value"><?= $verifiedToday ?></span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="<?= url('transactions?status=failed') ?>" class="text-decoration-none">
            <div class="notify-card">
                <div class="notify-icon bg-danger-subtle text-danger"><i class="fa-solid fa-circle-xmark"></i></div>
                <div class="notify-body">
                    <span class="notify-label">Failed Today</span>
                    <span class="notify-value"><?= $failedToday ?></span>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Stat cards -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-subtle text-primary"><i class="fa-solid fa-wallet"></i></div>
            <div class="stat-body">
                <span class="stat-label">Total Revenue</span>
                <span class="stat-value"><?= e(number_format($totalRevenue, 0)) ?></span>
                <span class="stat-hint"><i class="fa-solid fa-circle-check me-1 text-success"></i><?= e(number_format($totalVerified, 0)) ?> verified payments</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-subtle text-success"><i class="fa-solid fa-arrows-rotate"></i></div>
            <div class="stat-body">
                <span class="stat-label">Today's Transactions</span>
                <span class="stat-value"><?= $todayCount ?></span>
                <span class="stat-hint"><i class="fa-regular fa-calendar me-1 text-primary"></i>processed today</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-danger-subtle text-danger"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div class="stat-body">
                <span class="stat-label">Failed Verifications</span>
                <span class="stat-value"><?= $totalFailed ?></span>
                <span class="stat-hint"><i class="fa-solid fa-shield me-1 text-warning"></i>flagged for review</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-info-subtle text-info"><i class="fa-solid fa-users"></i></div>
            <div class="stat-body">
                <span class="stat-label">Active Users</span>
                <span class="stat-value"><?= $userCount ?></span>
                <span class="stat-hint"><i class="fa-solid fa-receipt me-1 text-secondary"></i><?= $receiptCount ?> receipts issued</span>
            </div>
        </div>
    </div>
</div>

<!-- Quick actions -->
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="<?= url('verify') ?>" class="btn btn-primary shadow-sm"><i class="fa-solid fa-shield-halved me-2"></i>Verify a Transaction</a>
    <?php if ($user['role'] === 'admin'): ?>
        <a href="<?= url('transactions/create') ?>" class="btn btn-success shadow-sm"><i class="fa-solid fa-plus me-2"></i>Add Transaction</a>
    <?php endif; ?>
    <a href="<?= url('transactions') ?>" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-right-arrow-left me-2"></i>Browse Transactions</a>
    <?php if ($user['role'] === 'admin'): ?>
        <a href="<?= url('reports') ?>" class="btn btn-outline-secondary"><i class="fa-solid fa-chart-pie me-2"></i>Generate Report</a>
    <?php endif; ?>
</div>

<div class="row g-3">
    <!-- 7-day revenue chart -->
    <div class="col-lg-8">
        <div class="card dashboard-card h-100">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-chart-line me-2 text-primary"></i>Revenue — Last 7 Days</span>
                <span class="badge text-bg-light border"><?= DEFAULT_CURRENCY ?></span>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="110"
                        data-labels='<?= e(json_encode($dayLabels)) ?>'
                        data-revenue='<?= e(json_encode($dayRevenue)) ?>'
                        data-verified='<?= e(json_encode($dayVerified)) ?>'></canvas>
            </div>
        </div>
    </div>

    <!-- Activity feed -->
    <div class="col-lg-4">
        <div class="card dashboard-card h-100">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-clock-rotate-left me-2 text-success"></i>Recent Activity</span>
                <i class="fa-regular fa-bell text-muted"></i>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentActivity)): ?>
                    <div class="empty-state p-4">
                        <i class="fa-regular fa-clock text-muted"></i>
                        <p class="mb-0">No activity recorded yet.</p>
                    </div>
                <?php else: ?>
                    <ul class="activity-list">
                        <?php foreach ($recentActivity as $log): ?>
                            <li>
                                <div class="activity-dot bg-primary"></div>
                                <div class="activity-content">
                                    <span class="activity-text"><?= e(audit_action_label($log['action'])) ?></span>
                                    <span class="activity-detail text-truncate"><?= e($log['details'] ?? ($log['user_name'] ?? 'System')) ?></span>
                                </div>
                                <span class="activity-time"><?= date('H:i', strtotime($log['created_at'])) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent transactions -->
    <div class="col-12">
        <div class="card dashboard-card">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-arrow-right-arrow-left me-2 text-warning"></i>Recent Transactions</span>
                <a href="<?= url('transactions') ?>" class="btn btn-sm btn-outline-primary">View all <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 dashboard-table">
                        <thead>
                            <tr>
                                <th>M-Pesa Code</th>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th class="text-end">Amount</th>
                                <th>Status</th>
                                <th>Verified At</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentTransactions)): ?>
                                <tr><td colspan="7">
                                    <div class="empty-state p-4">
                                        <i class="fa-regular fa-folder-open text-muted"></i>
                                        <p class="mb-0">No transactions yet. <a href="<?= url('verify') ?>">Verify your first payment</a>.</p>
                                    </div>
                                </td></tr>
                            <?php else: ?>
                                <?php foreach ($recentTransactions as $t): ?>
                                    <tr>
                                        <td><code class="mpesa-code"><?= e($t['mpesa_code']) ?></code></td>
                                        <td class="fw-medium"><?= e($t['customer_name'] ?? '—') ?></td>
                                        <td class="text-muted"><?= e($t['phone']) ?></td>
                                        <td class="text-end fw-semibold"><?= e(money($t['amount'])) ?></td>
                                        <td><?= status_badge($t['status']) ?></td>
                                        <td class="text-muted small"><?= e(format_datetime($t['verified_at'])) ?></td>
                                        <td class="text-end">
                                            <a href="<?= url('transactions/show/' . $t['id']) ?>" class="btn btn-sm btn-outline-secondary">
                                                <i class="fa-solid fa-eye me-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
