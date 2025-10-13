<?php
session_start();
require_once '../includes/db_connect.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$sql = "SELECT password_hash FROM admin_users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($hash);
    $stmt->fetch();

    if (password_verify($password, $hash)) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: dashboard.php");
        exit;
    }
}

echo "Invalid username or password.";
$stmt->close();
$conn->close();
?>