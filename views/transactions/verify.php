<?php
/**
 * Verify Transaction view (SRS 4.2.3) — MANUAL verification workflow.
 * Search for a transaction recorded in the local database, review its
 * details and mark it Verified or Failed. Variables: $code, $transaction,
 * $notFound, $isAdmin.
 */
$t = $transaction;
?>
<div class="row g-4">
    <div class="col-lg-7">
        <!-- Search -->
        <div class="card dashboard-card">
            <div class="card-header bg-transparent">
                <i class="fa-solid fa-magnifying-glass me-2 text-primary"></i>Search a Transaction
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    Enter the <strong>transaction code</strong> to look up a payment that was
                    recorded in the local database (e.g. <code>QHJ7K8L9MN</code>).
                </p>

                <form method="get" action="<?= url('verify') ?>" id="verifyForm" autocomplete="off">
                    <label for="mpesa_code" class="form-label fw-semibold">Transaction Code</label>
                    <div class="input-group input-group-lg mb-3">
                        <span class="input-group-text"><i class="fa-solid fa-qrcode"></i></span>
                        <input type="text" class="form-control text-uppercase code-input"
                               id="mpesa_code" name="code"
                               value="<?= e($code) ?>"
                               placeholder="Enter 10-character code"
                               maxlength="10" autofocus>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100" id="verifyBtn">
                        <span class="btn-normal"><i class="fa-solid fa-magnifying-glass me-2"></i>Search Transaction</span>
                        <span class="btn-loading d-none">
                            <span class="spinner-border spinner-border-sm me-2"></span>Searching…
                        </span>
                    </button>
                </form>

                <?php if ($notFound): ?>
                    <div class="alert alert-danger mt-4 mb-0">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                        <strong>No transaction found</strong> with code
                        <code><?= e($code) ?></code>. Check the code and try again, or
                        <a href="<?= url('transactions/create') ?>" class="alert-link">record a new transaction</a>.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($t !== null): ?>
            <!-- Transaction details -->
            <div class="card dashboard-card mt-3">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <span><i class="fa-solid fa-receipt me-2 text-primary"></i>Transaction Details</span>
                    <code class="mpesa-code"><?= e($t['mpesa_code']) ?></code>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0 detail-table">
                        <tbody>
                            <tr><th>Transaction Code</th><td><code><?= e($t['mpesa_code']) ?></code></td></tr>
                            <tr><th>Sender Name</th><td class="fw-medium"><?= e($t['customer_name'] ?? '—') ?></td></tr>
                            <tr><th>Sender Phone</th><td><?= e($t['phone']) ?></td></tr>
                            <tr><th>Amount</th><td class="fw-semibold fs-5"><?= e(money($t['amount'])) ?></td></tr>
                            <tr><th>Transaction Date</th><td><?= e(format_datetime($t['created_at'])) ?></td></tr>
                            <tr><th>Current Status</th><td><?= status_badge($t['status']) ?></td></tr>
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

            <!-- Actions -->
            <div class="card dashboard-card mt-3">
                <div class="card-body">
                    <?php if ($t['status'] === 'pending' && $isAdmin): ?>
                        <div class="d-grid gap-2 d-md-flex">
                            <form method="post" action="<?= url('transactions/verify') ?>" class="flex-fill"
                                  data-confirm="Verify this payment? The transaction will be marked as VERIFIED and a receipt will be generated.">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="fa-solid fa-circle-check me-2"></i>Verify Payment
                                </button>
                            </form>
                            <form method="post" action="<?= url('transactions/fail') ?>" class="flex-fill"
                                  data-confirm="Mark this transaction as FAILED? This action records the payment as unsuccessful.">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
                                <button type="submit" class="btn btn-outline-danger btn-lg w-100">
                                    <i class="fa-solid fa-circle-xmark me-2"></i>Mark as Failed
                                </button>
                            </form>
                        </div>
                    <?php elseif ($t['status'] === 'pending' && !$isAdmin): ?>
                        <div class="alert alert-info mb-0">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            This transaction is <strong>pending verification</strong>. Only an administrator
                            can verify the payment or mark it as failed.
                        </div>
                    <?php elseif ($t['status'] === 'verified'): ?>
                        <div class="alert alert-success mb-3">
                            <i class="fa-solid fa-circle-check me-2"></i>
                            <strong>This transaction has already been verified.</strong>
                            <?= e(money((float) $t['amount'])) ?> was received from
                            <?= e($t['customer_name'] ?? $t['phone']) ?> on
                            <?= e(format_datetime($t['verified_at'])) ?>.
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success btn-lg" disabled>
                                <i class="fa-solid fa-circle-check me-2"></i>Verify Payment
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-lg" disabled>
                                <i class="fa-solid fa-circle-xmark me-2"></i>Mark as Failed
                            </button>
                            <a href="<?= url('receipt/' . $t['id']) ?>" class="btn btn-primary btn-lg ms-auto">
                                <i class="fa-solid fa-print me-2"></i>View Receipt
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger mb-3">
                            <i class="fa-solid fa-circle-xmark me-2"></i>
                            <strong>This transaction has already been marked as failed.</strong>
                            It was reviewed on <?= e(format_datetime($t['verified_at'])) ?>.
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success btn-lg" disabled>
                                <i class="fa-solid fa-circle-check me-2"></i>Verify Payment
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-lg" disabled>
                                <i class="fa-solid fa-circle-xmark me-2"></i>Mark as Failed
                            </button>
                            <a href="<?= url('transactions/show/' . $t['id']) ?>" class="btn btn-outline-secondary btn-lg ms-auto">
                                <i class="fa-solid fa-eye me-2"></i>Full Details
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-5">
        <!-- How verification works -->
        <div class="card dashboard-card">
            <div class="card-header bg-transparent"><i class="fa-solid fa-circle-info me-2 text-info"></i>How Verification Works</div>
            <div class="card-body">
                <ol class="how-it-works">
                    <li>
                        <span class="how-num">1</span>
                        <div><strong>Administrator records a transaction</strong> — the payment is saved with its code, sender and amount.</div>
                    </li>
                    <li>
                        <span class="how-num">2</span>
                        <div><strong>Transaction is stored as Pending</strong> — it is now awaiting verification in the local database.</div>
                    </li>
                    <li>
                        <span class="how-num">3</span>
                        <div><strong>Administrator searches using the transaction code</strong> — the system displays the full transaction details.</div>
                    </li>
                    <li>
                        <span class="how-num">4</span>
                        <div><strong>Transaction details are reviewed</strong> — the sender, amount and code are confirmed against the customer.</div>
                    </li>
                    <li>
                        <span class="how-num">5</span>
                        <div><strong>Transaction is marked Verified or Failed</strong> — the database is updated and reports/dashboard refresh automatically.</div>
                    </li>
                </ol>
            </div>
        </div>

        <!-- Manual system notice -->
        <div class="card dashboard-card mt-3">
            <div class="card-header bg-transparent"><i class="fa-solid fa-shield-halved me-2 text-success"></i><?= e(APP_MODE_LABEL) ?></div>
            <div class="card-body small text-muted">
                <p class="mb-2"><i class="fa-solid fa-circle-check text-success me-2"></i><?= e(APP_MODE_DESCRIPTION) ?></p>
                <p class="mb-2"><i class="fa-solid fa-circle-check text-success me-2"></i>Records are stored securely with encrypted sessions and CSRF protection.</p>
                <p class="mb-0"><i class="fa-solid fa-circle-check text-success me-2"></i>A verified payment is immutable — no duplicates are possible.</p>
            </div>
        </div>

        <?php if ($isAdmin): ?>
            <a href="<?= url('transactions/create') ?>" class="btn btn-outline-primary w-100 mt-3">
                <i class="fa-solid fa-plus me-2"></i>Record a New Transaction
            </a>
        <?php endif; ?>
    </div>
</div>
