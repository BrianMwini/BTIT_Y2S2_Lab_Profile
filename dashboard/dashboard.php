<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>

<body>

<h2>Welcome,
<?php echo htmlspecialchars($_SESSION['fullname']); ?>
</h2>

<p>You have successfully logged in.</p>

<p>Role:
<strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong>
</p>

<a href="../auth/logout.php">Logout</a>

</body>
</html>