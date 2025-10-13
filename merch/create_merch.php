<?php
require_once '../includes/db_connect.php';

$username = 'merch1';
$password = 'Merch@123';
$hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO merch_users (username, password_hash) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $hash);

if ($stmt->execute()) {
    echo "Merch user created successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>