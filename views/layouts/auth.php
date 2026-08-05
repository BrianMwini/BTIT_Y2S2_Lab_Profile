<?php
/** Centered auth layout (login / register). Variables: $content, $title */
$pageTitle = $title ?? 'Sign in';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — <?= e(APP_NAME) ?></title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💳</text></svg>">
    <link rel="stylesheet" href="<?= url('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/vendor/fontawesome/all.min.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/app.css') ?>">
</head>
<body class="auth-body">

    <div class="auth-bg-shape auth-bg-shape-1"></div>
    <div class="auth-bg-shape auth-bg-shape-2"></div>

    <div class="auth-wrap">
        <div class="auth-brand">
            <div class="auth-brand-icon"><i class="fa-solid fa-mobile-screen-button"></i></div>
            <h1 class="auth-brand-title"><?= e(APP_NAME) ?></h1>
            <p class="auth-brand-sub"><?= e(APP_FULL_NAME) ?></p>
        </div>

        <div class="auth-card shadow-lg">
            <?php require VIEWS_PATH . '/partials/flash.php'; ?>
            <?= $content ?>
        </div>

        <p class="auth-footer"><?= e(BUSINESS_TAGLINE) ?></p>
    </div>

<script src="<?= url('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= url('assets/js/app.js') ?>"></script>
</body>
</html>
