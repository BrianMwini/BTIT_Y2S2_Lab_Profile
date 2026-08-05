<?php
/** Transaction detail view (SRS 4.2.4 / 4.2.5). Variables: $transaction, $receipt */
$t = $transaction;
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h4 class="mb-1">Transaction <code class="mpesa-code ms-1"><?= e($t['mpesa_code']) ?></code></h4>
        <span class="text-muted small">Recorded <?= e(format_datetime($t['created_at'])) ?></span>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('transactions') ?>" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Back</a>
        <?php if ($receipt): ?>
            <a href="<?= url('receipt/' . $t['id']) ?>" class="btn btn-primary"><i class="fa-solid fa-print me-1"></i>View Receipt</a>
        <?php endif; ?>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <!-- Status banner -->
        <div class="card dashboard-card mb-3">
            <div class="card-body d-flex align-items-center gap-3">
                <?php
                $banner = match ($t['status']) {
                    'verified' => ['success', 'fa-circle-check', 'Payment confirmed — goods/services may be released.'],
                    'failed'   => ['danger', 'fa-circle-xmark', 'This transaction was marked as failed by the administrator.'],
                    default    => ['warning', 'fa-clock', 'This transaction is still pending verification.'],
                };
                ?>
                <div class="status-banner-icon bg-<?= $banner[0] ?>-subtle text-<?= $banner[0] ?>">
                    <i class="fa-solid <?= $banner[1] ?>"></i>
                </div>
                <div>
                    <div class="fw-semibold"><?= status_badge($t['status']) ?></div>
                    <span class="text-muted small"><?= $banner[2] ?></span>
                </div>
            </div>
        </div>

        <!-- Transaction details -->
        <div class="card dashboard-card">
            <div class="card-header bg-transparent"><i class="fa-solid fa-receipt me-2 text-primary"></i>Transaction Details</div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0 detail-table">
                    <tbody>
                        <tr><th>M-Pesa Code</th><td><code><?= e($t['mpesa_code']) ?></code></td></tr>
                        <tr><th>Customer</th><td class="fw-medium"><?= e($t['customer_name'] ?? '—') ?></td></tr>
                        <tr><th>Phone</th><td><?= e($t['phone']) ?></td></tr>
                        <tr><th>Amount</th><td class="fw-semibold fs-5"><?= e(money($t['amount'])) ?></td></tr>
                        <tr><th>Status</th><td><?= status_badge($t['status']) ?></td></tr>
                        <tr>
                            <th>Verified By</th>
                            <td>
                                <?php if ($t['verified_by'] !== null): ?>
                                    <?= e($t['verifier_name'] ?? '—') ?>
                                    <span class="text-muted small d-block"><?= e(format_datetime($t['verified_at'])) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">Not yet processed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Receipt card -->
        <div class="card dashboard-card">
            <div class="card-header bg-transparent"><i class="fa-solid fa-print me-2 text-success"></i>Receipt</div>
            <div class="card-body">
                <?php if ($receipt): ?>
                    <div class="receipt-mini text-center">
                        <i class="fa-solid fa-receipt fs-1 text-success mb-2"></i>
                        <h6 class="mb-1"><?= e($receipt['receipt_no']) ?></h6>
                        <span class="text-muted small">Generated <?= e(format_datetime($receipt['generated_at'])) ?><br>by <?= e($receipt['generated_by_name'] ?? '—') ?></span>
                        <a href="<?= url('receipt/' . $t['id']) ?>" class="btn btn-success w-100 mt-3">
                            <i class="fa-solid fa-print me-2"></i>Print / Save Receipt
                        </a>
                    </div>
                <?php else: ?>
                    <div class="empty-state p-3">
                        <i class="fa-regular fa-receipt text-muted"></i>
                        <p class="small mb-2">No receipt is available for this record.</p>
                        <?php if ($t['status'] === 'verified'): ?>
                            <a href="<?= url('receipt/' . $t['id']) ?>" class="btn btn-sm btn-outline-success"><i class="fa-solid fa-plus me-1"></i>Generate Receipt</a>
                        <?php else: ?>
                            <span class="badge text-bg-light border">Receipts are issued for verified payments</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pending actions (admin) -->
        <?php if ($t['status'] === 'pending' && $isAdmin): ?>
            <div class="card dashboard-card mt-3">
                <div class="card-header bg-transparent"><i class="fa-solid fa-shield-halved me-2 text-warning"></i>Verification Actions</div>
                <div class="card-body">
                    <p class="small text-muted">This transaction is awaiting verification. Choose an action:</p>
                    <div class="d-grid gap-2">
                        <form method="post" action="<?= url('transactions/verify') ?>"
                              data-confirm="Verify this payment? A receipt will be generated.">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
                            <button type="submit" class="btn btn-success w-100"><i class="fa-solid fa-circle-check me-2"></i>Verify Payment</button>
                        </form>
                        <form method="post" action="<?= url('transactions/fail') ?>"
                              data-confirm="Mark this transaction as FAILED?">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
                            <button type="submit" class="btn btn-outline-danger w-100"><i class="fa-solid fa-circle-xmark me-2"></i>Mark as Failed</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Audit info -->
        <div class="card dashboard-card mt-3">
            <div class="card-header bg-transparent"><i class="fa-solid fa-shield-halved me-2 text-info"></i>Integrity</div>
            <div class="card-body small text-muted">
                <p class="mb-2"><i class="fa-solid fa-circle-check text-success me-2"></i>Record stored securely in the central database.</p>
                <p class="mb-2"><i class="fa-solid fa-circle-check text-success me-2"></i>Verifier and verification time are recorded for audit.</p>
                <p class="mb-0"><i class="fa-solid fa-circle-check text-success me-2"></i>This record is immutable once processed.</p>
            </div>
        </div>
    </div>
</div>
