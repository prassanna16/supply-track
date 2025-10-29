<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../../includes/db_connect.php'; // Adjusted path

// Decode incoming JSON
$data = json_decode(file_get_contents("php://input"), true);
$styles = $data['styles'] ?? [];

if (empty($styles)) {
  echo json_encode(['products' => [], 'suppliers' => []]);
  exit;
}

// Sanitize and format style list for SQL
$escapedStyles = array_map(function($style) use ($conn) {
  return "'" . mysqli_real_escape_string($conn, $style) . "'";
}, $styles);
$styleList = implode(",", $escapedStyles);

// Fetch matching products by style
$productQuery = "
  SELECT id, buyer, style, description, department, size_range, qty, currency, target
  FROM products
  WHERE style IN ($styleList)
";
$productResult = mysqli_query($conn, $productQuery);

$products = [];
$supplierSet = [];

while ($row = mysqli_fetch_assoc($productResult)) {
  $productId = $row['id'];

  // Add product to list
  $products[] = [
    'id' => $productId,
    'buyer' => $row['buyer'],
    'style' => $row['style'],
    'description' => $row['description'],
    'department' => $row['department'],
    'size_range' => $row['size_range'],
    'qty' => $row['qty'],
    'currency' => $row['currency'],
    'target' => $row['target']
  ];

  // Fetch suppliers linked to this product
  $supplierQuery = "SELECT supplier_name FROM suppliers WHERE product_id = $productId";
  $supplierResult = mysqli_query($conn, $supplierQuery);
  while ($s = mysqli_fetch_assoc($supplierResult)) {
    $supplierSet[$s['supplier_name']] = true;
  }
}

// Return unique supplier names
$suppliers = array_keys($supplierSet);

// Send JSON response
echo json_encode([
  'products' => $products,
  'suppliers' => $suppliers
]);
?>