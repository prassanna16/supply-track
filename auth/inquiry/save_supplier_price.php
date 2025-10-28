<?php
include '../../db.php';

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