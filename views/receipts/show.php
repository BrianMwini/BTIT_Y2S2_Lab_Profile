<?php
/** Receipt view (SRS 4.2.5) — printable digital payment receipt. Variables: $receipt */
$r = $receipt;
?>
<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <div>
        <h4 class="mb-1"><i class="fa-solid fa-receipt me-2 text-success"></i>Payment Receipt</h4>
        <span class="text-muted small">Verified on <?= e(format_datetime($r['verified_at'])) ?></span>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary" onclick="window.print()">
            <i class="fa-solid fa-print me-2"></i>Print
        </button>
        <a href="<?= url('transactions/show/' . $r['transaction_id']) ?>" class="btn btn-outline-secondary">Back</a>
    </div>
</div>

<div class="receipt-sheet shadow-sm mx-auto">
    <!-- Header -->
    <div class="receipt-header">
        <div class="receipt-brand">
            <div class="receipt-logo"><i class="fa-solid fa-mobile-screen-button"></i></div>
            <div>
                <h5 class="mb-0"><?= e(BUSINESS_NAME) ?></h5>
                <small class="d-block opacity-75"><?= e(BUSINESS_ADDRESS) ?></small>
                <small class="d-block opacity-75"><?= e(BUSINESS_PHONE) ?> · <?= e(BUSINESS_EMAIL) ?></small>
            </div>
        </div>
        <div class="receipt-title-box">
            <span class="receipt-kicker">Payment Verification</span>
            <span class="receipt-no"><?= e($r['receipt_no']) ?></span>
        </div>
    </div>

    <div class="receipt-status text-center py-3">
        <i class="fa-solid fa-circle-check me-2"></i>PAYMENT VERIFIED
    </div>

    <!-- Details -->
    <table class="table receipt-details">
        <tbody>
            <tr><td class="label">M-Pesa Confirmation Code</td><td class="value"><code><?= e($r['mpesa_code']) ?></code></td></tr>
            <tr><td class="label">Customer</td><td class="value fw-semibold"><?= e($r['customer_name'] ?? '—') ?></td></tr>
            <tr><td class="label">Customer Phone</td><td class="value"><?= e($r['phone']) ?></td></tr>
            <tr><td class="label">Amount Paid</td><td class="value amount"><?= e(money($r['amount'])) ?></td></tr>
            <tr><td class="label">Verification Date</td><td class="value"><?= e(format_datetime($r['verified_at'], 'd M Y, g:i A')) ?></td></tr>
            <tr><td class="label">Verified By</td><td class="value"><?= e($r['generated_by_name'] ?? '—') ?></td></tr>
            <tr><td class="label">Status</td><td class="value">Verified</td></tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="receipt-footer">
        <div class="receipt-amount-box">
            <span class="small opacity-75">TOTAL CONFIRMED</span>
            <strong><?= e(money($r['amount'])) ?></strong>
        </div>
        <p class="receipt-thanks mb-0">Thank you for your payment! This receipt confirms that your M-Pesa payment has been verified by <?= e(BUSINESS_NAME) ?>.</p>
    </div>

    <div class="receipt-meta">
        <span>Receipt #<?= e($r['receipt_no']) ?></span>
        <span>Generated <?= e(format_datetime($r['generated_at'])) ?></span>
        <span>by <?= e($r['generated_by_name'] ?? '—') ?></span>
    </div>
</div>

<p class="text-center text-muted small mt-4 no-print">
    Keep this receipt for your records. For queries contact <?= e(BUSINESS_PHONE) ?>.
</p>
