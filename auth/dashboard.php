<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: auth/login_admin.html");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head><title>Dashboard</title></head>
<body>
  <h1>Welcome, Admin!</h1>
  <a href="logout.php">Logout</a>
</body>
</html>