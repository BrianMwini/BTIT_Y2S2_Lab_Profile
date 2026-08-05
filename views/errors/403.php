<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Access Denied | <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= url('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/vendor/fontawesome/all.min.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/app.css') ?>">
</head>
<body class="error-body">
    <div class="error-wrap">
        <div class="error-code text-danger">403</div>
        <i class="fa-solid fa-lock text-danger fs-1 mb-3"></i>
        <h2 class="mb-2">Access denied</h2>
        <p class="text-muted mb-4">You do not have permission to access this page. This area is restricted to administrators.</p>
        <a href="<?= url('') ?>" class="btn btn-primary px-4"><i class="fa-solid fa-house me-2"></i>Back to Dashboard</a>
    </div>
</body>
</html>
