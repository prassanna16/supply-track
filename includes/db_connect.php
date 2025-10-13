<?php
$host = 'localhost';
$db   = 'u994782675_supplytrack';
$user = 'u994782675_Avis';
$pass = 'Avis@123456';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>