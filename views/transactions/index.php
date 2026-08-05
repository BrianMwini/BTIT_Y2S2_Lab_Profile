<?php
/** Transactions list view (SRS 4.2.4) — search, filter and review records. */
// http_build_query RFC3986-encodes braces, so restore the literal {page}
// placeholder for the pagination partial.
$baseUrl = url('transactions') . '?' . str_replace(['%7B', '%7D'], ['{', '}'], http_build_query(array_merge(array_filter($filters, fn($v) => $v !== ''), ['page' => '{page}'])));
?>
<div class="card dashboard-card">
    <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span><i class="fa-solid fa-arrow-right-arrow-left me-2 text-primary"></i>Transaction Records</span>
        <div class="d-flex align-items-center gap-2">
            <?php if ($user['role'] === 'admin'): ?>
                <a href="<?= url('transactions/create') ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Transaction</a>
            <?php endif; ?>
            <span class="badge text-bg-light border"><?= $total ?> record<?= $total === 1 ? '' : 's' ?></span>
        </div>
    </div>

    <!-- Search / filter bar -->
    <div class="card-body border-bottom">
        <form method="get" action="<?= url('transactions') ?>" class="row g-2 align-items-end" id="filterForm">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Transaction Code</label>
                <input type="text" class="form-control form-control-sm" name="code" value="<?= e($filters['code']) ?>" placeholder="SJX3K9Q2PL">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Phone</label>
                <input type="text" class="form-control form-control-sm" name="phone" value="<?= e($filters['phone']) ?>" placeholder="07XXXXXXXX">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Customer</label>
                <input type="text" class="form-control form-control-sm" name="customer" value="<?= e($filters['customer']) ?>" placeholder="Name">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Status</label>
                <select class="form-select form-select-sm" name="status">
                    <option value="">All statuses</option>
                    <option value="verified" <?= $filters['status'] === 'verified' ? 'selected' : '' ?>>Verified</option>
                    <option value="failed" <?= $filters['status'] === 'failed' ? 'selected' : '' ?>>Failed</option>
                    <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Verified By</label>
                <select class="form-select form-select-sm" name="verifier">
                    <option value="">Any verifier</option>
                    <?php foreach ($verifiers as $v): ?>
                        <option value="<?= (int) $v['id'] ?>" <?= (string) $filters['verifier'] === (string) $v['id'] ? 'selected' : '' ?>><?= e($v['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <div class="row g-1">
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">From</label>
                        <input type="date" class="form-control form-control-sm" name="date_from" value="<?= e($filters['date_from']) ?>">
                    </div>
                    <div class="col-6">
                        <label class="form-label small text-muted mb-1">To</label>
                        <input type="date" class="form-control form-control-sm" name="date_to" value="<?= e($filters['date_to']) ?>">
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-fill"><i class="fa-solid fa-magnifying-glass me-1"></i>Apply</button>
                <a href="<?= url('transactions') ?>" class="btn btn-outline-secondary btn-sm" title="Clear filters"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>
    </div>

    <!-- Records table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 dashboard-table">
            <thead>
                <tr>
                    <th>Transaction Code</th>
                    <th>Sender</th>
                    <th>Phone</th>
                    <th class="text-end">Amount</th>
                    <th>Status</th>
                    <th>Verified By</th>
                    <th>Date</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="8">
                            <div class="empty-state p-5">
                                <i class="fa-regular fa-folder-open text-muted"></i>
                                <h6 class="mt-3 mb-1">No transactions found</h6>
                                <p class="text-muted small mb-0">
                                    <?= $total > 0 ? 'No records match your filters. Try adjusting the search criteria.' : 'No payments verified yet. <a href="' . url('verify') . '">Verify your first transaction</a>.' ?>
                                </p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $t): ?>
                        <tr>
                            <td><code class="mpesa-code"><?= e($t['mpesa_code']) ?></code></td>
                            <td class="fw-medium"><?= e($t['customer_name'] ?? '—') ?></td>
                            <td class="text-muted"><?= e($t['phone']) ?></td>
                            <td class="text-end fw-semibold"><?= e(money($t['amount'])) ?></td>
                            <td><?= status_badge($t['status']) ?></td>
                            <td>
                                <?php if ($t['verified_by'] !== null): ?>
                                    <?= e($t['verifier_name'] ?? '—') ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small"><?= e(format_datetime($t['verified_at'] ?? $t['created_at'])) ?></td>
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

    <?php if (!empty($rows)): ?>
        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="small text-muted">Showing page <?= $page ?> of <?= $pages ?> (<?= $total ?> total)</span>
            <?php require VIEWS_PATH . '/partials/pagination.php'; ?>
        </div>
    <?php endif; ?>
</div>
