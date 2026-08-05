<?php
/** Login view (SRS 4.2.1 Login Interface). Variables: $errors */
$errors = $errors ?? [];
$old = $old ?? [];
?>
<h2 class="auth-card-title">Sign In</h2>
<p class="auth-card-sub">Access the M-Pesa payment verification dashboard</p>

<form method="post" action="<?= url('login') ?>" novalidate>
    <?= csrf_field() ?>

    <?php if (!empty($errors['auth'])): ?>
        <div class="alert alert-danger py-2 small"><i class="fa-solid fa-circle-exclamation me-1"></i><?= e($errors['auth']) ?></div>
    <?php endif; ?>

    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
            <input type="text" class="form-control <?= !empty($errors['username']) ? 'is-invalid' : '' ?>"
                   id="username" name="username" value="<?= e($old['username'] ?? '') ?>"
                   placeholder="e.g. admin" autofocus required>
        </div>
        <?php if (!empty($errors['username'])): ?><div class="invalid-feedback d-block"><?= e($errors['username']) ?></div><?php endif; ?>
    </div>

    <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
            <input type="password" class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
                   id="password" name="password" placeholder="••••••••" required>
            <button class="btn btn-outline-secondary" type="button" tabindex="-1" data-toggle-password="password">
                <i class="fa-regular fa-eye"></i>
            </button>
        </div>
        <?php if (!empty($errors['password'])): ?><div class="invalid-feedback d-block"><?= e($errors['password']) ?></div><?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary w-100 btn-lg">
        <i class="fa-solid fa-right-to-bracket me-2"></i>Sign In
    </button>
</form>

<div class="auth-divider"><span>or</span></div>

<p class="text-center mb-0 small">
    Don't have an account? <a href="<?= url('register') ?>" class="fw-semibold">Create staff account</a>
</p>
