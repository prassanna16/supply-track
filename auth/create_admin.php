<?php
// create_admin.php

$host = 'localhost';
$db   = 'u994782675_supplytrack';
$user = 'u994782675_Avis';
$pass = 'Avis@123456';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = 'admin';
$password = 'Admin@123';
$hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO admin_users (username, password_hash) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $hash);

if ($stmt->execute()) {
    echo "Admin user created successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>