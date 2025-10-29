<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../../includes/db_connect.php'; // Adjusted path

$query = "SELECT DISTINCT style FROM products ORDER BY style ASC";
$result = mysqli_query($conn, $query);

$styles = [];
while ($row = mysqli_fetch_assoc($result)) {
  $styles[] = $row['style'];
}

echo json_encode($styles);
?>