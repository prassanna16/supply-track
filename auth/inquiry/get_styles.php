<?php
require_once '../../includes/db_connect.php'; // Adjust path if needed

$query = "SELECT DISTINCT style FROM products ORDER BY style ASC";
$result = mysqli_query($conn, $query);

$styles = [];
while ($row = mysqli_fetch_assoc($result)) {
  $styles[] = $row['style'];
}

echo json_encode($styles);
?>