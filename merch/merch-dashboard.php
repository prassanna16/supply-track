<?php
session_start();
if (!isset($_SESSION['merch_logged_in'])) {
    header("Location: login_merch.html");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head><title>Merch Dashboard</title></head>
<body>
  <h1>Welcome, Merch!</h1>
  <p>You are logged in.</p>
  <a href="logout_merch.php">Logout</a>
</body>
</html>