<?php
include '../includes/db_connect.php';

$id = $_POST['id'];
$style = $_POST['style'];
$supplier = $_POST['supplier'];
$product = $_POST['product'];
$quantity = $_POST['quantity'];

$sql = "UPDATE inquiries SET style=?, supplier=?, product=?, quantity=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $style, $supplier, $product, $quantity, $id);
$stmt->execute();

// Redirect back with success flag
header("Location: inquiries_new.html?id=$id&updated=true");
?>