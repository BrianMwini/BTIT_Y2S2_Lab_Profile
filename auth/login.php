<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | M-Pesa Verification System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="login-container">

    <div class="login-card">

        <h1>M-Pesa Verification System</h1>

        <form action="authenticate.php" method="POST">

            <input
                type="email"
                name="email"
                placeholder="Email Address"
                required
            >

            <input
                type="password"
                name="password"
                placeholder="Password"
                required
            >

            <button type="submit">
                Login
            </button>

        </form>

    </div>

</div>

</body>
</html>