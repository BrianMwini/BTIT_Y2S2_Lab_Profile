<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found | <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= url('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/vendor/fontawesome/all.min.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/app.css') ?>">
</head>
<body class="error-body">
    <div class="error-wrap">
        <div class="error-code">404</div>
        <i class="fa-solid fa-magnifying-glass-location text-muted fs-1 mb-3"></i>
        <h2 class="mb-2">Page not found</h2>
        <p class="text-muted mb-4">The page you are looking for does not exist or has been moved.</p>
        <a href="<?= url('') ?>" class="btn btn-primary px-4"><i class="fa-solid fa-house me-2"></i>Back to Dashboard</a>
    </div>
</body>
</html>
