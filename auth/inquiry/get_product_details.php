<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once '../../includes/db_connect.php';

$input = json_decode(file_get_contents("php://input"), true);
$styles = $input['styles'] ?? [];

if (empty($styles)) {
  echo json_encode([]);
  exit;
}

$escapedStyles = array_map(function($name) use ($conn) {
  return "'" . mysqli_real_escape_string($conn, $name) . "'";
}, $styles);

$stylelist = implode(',', $escapedStyles);

$query = "
  SELECT
    p.id AS sno, p.style AS buyer_style, p.description, p.department,
    p.size_range, p.qty, p.currency, p.target,
    s.name AS supplier_name
  FROM products p
  LEFT JOIN supplies s ON p.id = s.product_id
  WHERE p.style IN ($stylelist)
  ORDER BY p.style, s.name
";

$result = mysqli_query($conn, $query);
$details = [];

while ($row = mysqli_fetch_assoc($result)) {
  $details[] = $row;
}

echo json_encode($details);