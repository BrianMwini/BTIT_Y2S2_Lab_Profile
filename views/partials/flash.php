<?php
/** Flash message partial — renders queued alerts with dismiss buttons. */
$messages = \App\Core\Flash::pull();
if (empty($messages)) {
    return;
}
?>
<div class="flash-container">
    <?php foreach ($messages as $msg): ?>
        <?php
        $icon = match ($msg['type']) {
            'success' => 'fa-circle-check',
            'danger'  => 'fa-circle-exclamation',
            'warning' => 'fa-triangle-exclamation',
            default   => 'fa-circle-info',
        };
        ?>
        <div class="alert alert-<?= e($msg['type']) ?> alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid <?= $icon ?> me-2"></i>
            <?= e($msg['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endforeach; ?>
</div>
