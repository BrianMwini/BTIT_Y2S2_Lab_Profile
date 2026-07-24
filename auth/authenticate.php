<?php
session_start();

require_once "../config/database.php";

$email = trim($_POST['email']);
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if ($user = mysqli_fetch_assoc($result)) {

    if (password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];

        header("Location: ../dashboard/dashboard.php");
        exit();

    }
}

header("Location: login.php?error=1");
exit();