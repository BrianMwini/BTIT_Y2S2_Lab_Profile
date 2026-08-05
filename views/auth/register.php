<?php
/** Register view (SRS FR-01). Variables: $errors, $old */
$errors = $errors ?? [];
$old = $old ?? [];
$field = fn(string $key): string => isset($errors[$key]) ? ' is-invalid' : '';
?>
<h2 class="auth-card-title">Create Account</h2>
<p class="auth-card-sub">New accounts are registered as <strong>Business Staff</strong></p>

<div class="alert alert-info py-2 small mb-3">
    <i class="fa-solid fa-user-clock me-1"></i>
    Registrations require <strong>administrator approval</strong> before you can log in.
</div>

<form method="post" action="<?= url('register') ?>" novalidate>
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="full_name" class="form-label">Full Name</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-id-card"></i></span>
            <input type="text" class="form-control<?= $field('full_name') ?>" id="full_name" name="full_name"
                   value="<?= e($old['full_name'] ?? '') ?>" placeholder="e.g. Jane Wanjiru" required>
        </div>
        <?php if (!empty($errors['full_name'])): ?><div class="invalid-feedback d-block"><?= e($errors['full_name']) ?></div><?php endif; ?>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-md-6">
            <label for="username" class="form-label">Username</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                <input type="text" class="form-control<?= $field('username') ?>" id="username" name="username"
                       value="<?= e($old['username'] ?? '') ?>" placeholder="jane_staff" required>
            </div>
            <?php if (!empty($errors['username'])): ?><div class="invalid-feedback d-block"><?= e($errors['username']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-6">
            <label for="phone" class="form-label">Phone <span class="text-muted">(optional)</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                <input type="text" class="form-control" id="phone" name="phone"
                       value="<?= e($old['phone'] ?? '') ?>" placeholder="07XXXXXXXX">
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
            <input type="email" class="form-control<?= $field('email') ?>" id="email" name="email"
                   value="<?= e($old['email'] ?? '') ?>" placeholder="jane@example.com" required>
        </div>
        <?php if (!empty($errors['email'])): ?><div class="invalid-feedback d-block"><?= e($errors['email']) ?></div><?php endif; ?>
    </div>

    <div class="row g-2 mb-4">
        <div class="col-md-6">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" class="form-control<?= $field('password') ?>" id="password" name="password"
                       placeholder="Min 8 chars, letters + numbers" required>
            </div>
            <?php if (!empty($errors['password'])): ?><div class="invalid-feedback d-block"><?= e($errors['password']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-6">
            <label for="password2" class="form-label">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" class="form-control<?= $field('password2') ?>" id="password2" name="password2" required>
            </div>
            <?php if (!empty($errors['password2'])): ?><div class="invalid-feedback d-block"><?= e($errors['password2']) ?></div><?php endif; ?>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 btn-lg">
        <i class="fa-solid fa-user-plus me-2"></i>Register
    </button>
</form>

<p class="text-center mt-4 mb-0 small">
    Already have an account? <a href="<?= url('login') ?>" class="fw-semibold">Sign in</a>
</p>
