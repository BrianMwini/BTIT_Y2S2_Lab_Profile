<?php
/** Reports view (SRS 4.2.6) — analytical reports, charts and exports. Admin only. */
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h4 class="mb-1"><i class="fa-solid fa-chart-pie me-2 text-primary"></i>Transaction Reports</h4>
        <span class="text-muted small">Statistical summaries and charts to support decision-making</span>
    </div>
    <div class="d-flex gap-2">
        <form method="get" action="<?= url('reports/export') ?>">
            <input type="hidden" name="from" value="<?= e($dateFrom) ?>">
            <input type="hidden" name="to" value="<?= e($dateTo) ?>">
            <input type="hidden" name="status" value="<?= e($status) ?>">
            <input type="hidden" name="verifier" value="<?= e($verifier) ?>">
            <button type="submit" class="btn btn-outline-success"><i class="fa-solid fa-file-csv me-2"></i>Export CSV</button>
        </form>
        <form method="post" action="<?= url('reports/generate') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="from" value="<?= e($dateFrom) ?>">
            <input type="hidden" name="to" value="<?= e($dateTo) ?>">
            <input type="hidden" name="status" value="<?= e($status) ?>">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-2"></i>Save Report</button>
        </form>
    </div>
</div>

<!-- Filters -->
<div class="card dashboard-card mb-4">
    <div class="card-body">
        <form method="get" action="<?= url('reports') ?>" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Date From</label>
                <input type="date" class="form-control" name="from" value="<?= e($dateFrom) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Date To</label>
                <input type="date" class="form-control" name="to" value="<?= e($dateTo) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Status</label>
                <select class="form-select" name="status">
                    <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All statuses</option>
                    <option value="verified" <?= $status === 'verified' ? 'selected' : '' ?>>Verified</option>
                    <option value="failed" <?= $status === 'failed' ? 'selected' : '' ?>>Failed</option>
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Verifier</label>
                <select class="form-select" name="verifier">
                    <option value="">Any verifier</option>
                    <?php foreach ($verifiers as $v): ?>
                        <option value="<?= (int) $v['id'] ?>" <?= (string) $verifier === (string) $v['id'] ? 'selected' : '' ?>><?= e($v['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="fa-solid fa-filter me-2"></i>Apply Filters</button>
            </div>
        </form>
    </div>
</div>

<!-- Summary cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-subtle text-primary"><i class="fa-solid fa-list"></i></div>
            <div class="stat-body">
                <span class="stat-label">Transactions</span>
                <span class="stat-value"><?= $stats['total'] ?></span>
                <span class="stat-hint">in selected period</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-subtle text-success"><i class="fa-solid fa-circle-check"></i></div>
            <div class="stat-body">
                <span class="stat-label">Verified</span>
                <span class="stat-value"><?= $stats['verified'] ?></span>
                <span class="stat-hint">successful confirmations</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-danger-subtle text-danger"><i class="fa-solid fa-circle-xmark"></i></div>
            <div class="stat-body">
                <span class="stat-label">Failed</span>
                <span class="stat-value"><?= $stats['failed'] ?></span>
                <span class="stat-hint">rejected codes</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning-subtle text-warning"><i class="fa-solid fa-sack-dollar"></i></div>
            <div class="stat-body">
                <span class="stat-label">Total Revenue</span>
                <span class="stat-value"><?= e(number_format($stats['revenue'], 0)) ?></span>
                <span class="stat-hint">avg <?= e(number_format($stats['average'], 0)) ?> per payment</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Daily trend -->
    <div class="col-lg-8">
        <div class="card dashboard-card h-100">
            <div class="card-header bg-transparent"><i class="fa-solid fa-chart-column me-2 text-primary"></i>Daily Transaction Trend</div>
            <div class="card-body">
                <canvas id="trendChart" height="120"
                        data-labels='<?= e(json_encode(array_column($series, 'day'))) ?>'
                        data-verified='<?= e(json_encode(array_map(fn($r) => (int) $r['verified'], $series))) ?>'
                        data-failed='<?= e(json_encode(array_map(fn($r) => (int) $r['failed'], $series))) ?>'></canvas>
            </div>
        </div>
    </div>
    <!-- Status distribution -->
    <div class="col-lg-4">
        <div class="card dashboard-card h-100">
            <div class="card-header bg-transparent"><i class="fa-solid fa-chart-pie me-2 text-success"></i>Status Distribution</div>
            <div class="card-body d-flex align-items-center">
                <div class="w-100">
                    <canvas id="statusChart" height="180"
                            data-verified='<?= $distribution['verified'] ?>'
                            data-failed='<?= $distribution['failed'] ?>'
                            data-pending='<?= $distribution['pending'] ?>'></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Records in window -->
    <div class="col-lg-8">
        <div class="card dashboard-card h-100">
            <div class="card-header bg-transparent">
                <span><i class="fa-solid fa-arrow-right-arrow-left me-2 text-warning"></i>Records in this period</span>
                <span class="badge text-bg-light border"><?= count($recentRows) ?> shown</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 dashboard-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Sender</th>
                            <th class="text-end">Amount</th>
                            <th>Status</th>
                            <th>Verified By</th>
                            <th>Verification Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentRows)): ?>
                            <tr><td colspan="6">
                                <div class="empty-state p-4">
                                    <i class="fa-regular fa-chart-bar text-muted"></i>
                                    <p class="mb-0 small">No transactions in the selected period.</p>
                                </div>
                            </td></tr>
                        <?php else: ?>
                            <?php foreach ($recentRows as $row): ?>
                                <tr>
                                    <td><code class="mpesa-code"><?= e($row['mpesa_code']) ?></code></td>
                                    <td><?= e($row['customer_name'] ?? '—') ?></td>
                                    <td class="text-end fw-medium"><?= e(money($row['amount'])) ?></td>
                                    <td><?= status_badge($row['status']) ?></td>
                                    <td>
                                        <?php if ($row['verified_by'] !== null): ?>
                                            <?= e($row['verifier_name'] ?? '—') ?>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small"><?= e(format_datetime($row['verified_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Report history -->
    <div class="col-lg-4">
        <div class="card dashboard-card h-100">
            <div class="card-header bg-transparent"><i class="fa-solid fa-clock-rotate-left me-2 text-info"></i>Report History</div>
            <div class="card-body p-0">
                <?php if (empty($reportHistory)): ?>
                    <div class="empty-state p-4">
                        <i class="fa-regular fa-file-lines text-muted"></i>
                        <p class="mb-0">No saved reports yet. Use "Save Report" to archive a report.</p>
                    </div>
                <?php else: ?>
                    <ul class="report-history list-unstyled mb-0">
                        <?php foreach ($reportHistory as $report): ?>
                            <li>
                                <div class="report-icon"><i class="fa-solid fa-file-lines"></i></div>
                                <div class="report-body">
                                    <span class="report-title text-truncate"><?= e($report['title']) ?></span>
                                    <small class="text-muted d-block">
                                        <?= e(date('d M Y, g:i A', strtotime($report['generated_at']))) ?> · <?= e($report['generated_by_name'] ?? '—') ?>
                                    </small>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
