<?php
// Show errors during development
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Force JSON response
header('Content-Type: application/json');

require_once '../../includes/db_connect.php';

// Safely decode incoming JSON
$input = json_decode(file_get_contents("php://input"), true);
$styles = $input['styles'] ?? [];

if (empty($styles)) {
  echo json_encode([]);
  exit;
}

// Escape styles for SQL
$escapedStyles = array_map(function($style) use ($conn) {
  return "'" . mysqli_real_escape_string($conn, $style) . "'";
}, $styles);

$styleList = implode(',', $escapedStyles);

// Build query
$query = "
  SELECT 
    p.id AS sno, p.style AS buyer_style, p.description, p.department, 
    p.size_range, p.qty, p.currency, p.target,
    s.name AS supplier_name
  FROM products p
  LEFT JOIN supplies s ON p.id = s.product_id
  WHERE p.style IN ($styleList)
  ORDER BY p.style, s.name
";

// Execute query
$result = mysqli_query($conn, $query);
$details = [];

if ($result) {
  while ($row = mysqli_fetch_assoc($result)) {
    $details[] = $row;
  }
} else {
  // Optional: log error for debugging
  error_log("Query failed: " . mysqli_error($conn));
}

// Return JSON
echo json_encode($details);