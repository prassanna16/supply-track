<?php
require_once __DIR__ . '/../../includes/db_connect.php';

if (!isset($conn) || $conn->connect_error) {
  echo "<div style='color:red; padding:10px; background:#fee; border:1px solid #f00;'>
          <strong>Database Error:</strong> " . htmlspecialchars($conn->connect_error) . "
        </div>";
  return; // Stop rendering the rest of the modal
}

$supplier_id = intval($_POST['supplier_id']);
$price = floatval($_POST['price']);
$product_id = intval($_POST['product_id']);

$query = "INSERT INTO supplier_prices (supplier_id, product_id, price)
          VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE price = VALUES(price)";
$stmt = $conn->prepare($query);
$stmt->bind_param("iid", $supplier_id, $product_id, $price);

if ($stmt->execute()) {
  echo "<span style='color:green;'>Price saved successfully.</span>";
} else {
  echo "<span style='color:red;'>Error saving price.</span>";
}

$stmt->close();
$conn->close();
?>