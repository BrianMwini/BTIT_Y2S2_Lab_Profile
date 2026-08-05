<?php
/**
 * Add Transaction view — administrators record a manual payment here.
 * The transaction is stored as Pending, then the page redirects to the
 * Verify Transaction page for review. Variables: $errors, $old.
 */
$errors = $errors ?? [];
$old = $old ?? [];
$field = fn(string $key): string => isset($errors[$key]) ? ' is-invalid' : '';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h4 class="mb-1"><i class="fa-solid fa-plus me-2 text-primary"></i>Add Transaction</h4>
        <span class="text-muted small">Record a payment, then verify it on the next page</span>
    </div>
    <a href="<?= url('transactions') ?>" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Back</a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card dashboard-card">
            <div class="card-header bg-transparent"><i class="fa-solid fa-receipt me-2 text-primary"></i>Transaction Details</div>
            <div class="card-body">
                <form method="post" action="<?= url('transactions/store') ?>" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="tx_code" class="form-label">Transaction Code <span class="text-muted">(optional)</span></label>
                        <input type="text" class="form-control form-control-lg code-input<?= $field('mpesa_code') ?>"
                               id="tx_code" name="mpesa_code"
                               value="<?= e($old['mpesa_code'] ?? '') ?>"
                               placeholder="Leave blank to auto-generate"
                               maxlength="10">
                        <div class="form-text">
                            <i class="fa-solid fa-wand-magic-sparkles me-1 text-primary"></i>
                            Leave blank to generate a unique 10-character code automatically (e.g. <code>QHJ7K8L9MN</code>).
                        </div>
                        <?php if (!empty($errors['mpesa_code'])): ?><div class="invalid-feedback d-block"><?= e($errors['mpesa_code']) ?></div><?php endif; ?>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-7">
                            <label for="sender_name" class="form-label">Sender Name</label>
                            <input type="text" class="form-control<?= $field('sender_name') ?>"
                                   id="sender_name" name="sender_name"
                                   value="<?= e($old['sender_name'] ?? '') ?>"
                                   placeholder="e.g. Jane Wanjiru" required>
                            <?php if (!empty($errors['sender_name'])): ?><div class="invalid-feedback d-block"><?= e($errors['sender_name']) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-5">
                            <label for="sender_phone" class="form-label">Sender Phone</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                                <input type="tel" class="form-control<?= $field('sender_phone') ?>"
                                       id="sender_phone" name="sender_phone"
                                       value="<?= e($old['sender_phone'] ?? '') ?>"
                                       placeholder="0712345678" required>
                            </div>
                            <?php if (!empty($errors['sender_phone'])): ?><div class="invalid-feedback d-block"><?= e($errors['sender_phone']) ?></div><?php endif; ?>
                        </div>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-md-7">
                            <label for="amount" class="form-label">Amount (<?= e(DEFAULT_CURRENCY) ?>)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-money-bill-wave"></i></span>
                                <input type="number" step="0.01" min="0.01" class="form-control<?= $field('amount') ?>"
                                       id="amount" name="amount"
                                       value="<?= e($old['amount'] ?? '') ?>"
                                       placeholder="e.g. 1500.00" required>
                            </div>
                            <?php if (!empty($errors['amount'])): ?><div class="invalid-feedback d-block"><?= e($errors['amount']) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-5">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" disabled>
                                <option value="pending" selected>Pending — awaiting verification</option>
                            </select>
                            <input type="hidden" name="status" value="pending">
                            <div class="form-text">
                                <i class="fa-solid fa-circle-info me-1 text-warning"></i>
                                Transactions are always recorded as <strong>Pending</strong> and verified afterwards.
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg px-4" id="storeBtn">
                        <span class="btn-normal"><i class="fa-solid fa-floppy-disk me-2"></i>Save Transaction &amp; Continue to Verification</span>
                        <span class="btn-loading d-none">
                            <span class="spinner-border spinner-border-sm me-2"></span>Saving…
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card dashboard-card">
            <div class="card-header bg-transparent"><i class="fa-solid fa-arrow-right-arrow-left me-2 text-info"></i>What Happens Next</div>
            <div class="card-body">
                <ol class="how-it-works">
                    <li>
                        <span class="how-num">1</span>
                        <div>The payment is saved as <span class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle px-2 py-1"><i class="fa-solid fa-clock me-1"></i>Pending</span>.</div>
                    </li>
                    <li>
                        <span class="how-num">2</span>
                        <div>You are taken straight to the <strong>Verify Transaction</strong> page, which searches the new record automatically.</div>
                    </li>
                    <li>
                        <span class="how-num">3</span>
                        <div>Review the details, then click <strong>Verify Payment</strong> or <strong>Mark as Failed</strong>.</div>
                    </li>
                    <li>
                        <span class="how-num">4</span>
                        <div>A digital receipt is generated for verified payments, and the dashboard/reports update automatically.</div>
                    </li>
                </ol>
            </div>
        </div>

        <div class="alert alert-success mt-3 mb-0">
            <i class="fa-solid fa-shield-halved me-2"></i>
            <strong><?= e(APP_MODE_LABEL) ?></strong><br>
            <span class="small"><?= e(APP_MODE_DESCRIPTION) ?></span>
        </div>
    </div>
</div>
