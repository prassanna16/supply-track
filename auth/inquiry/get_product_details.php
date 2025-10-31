<?php
// Show errors during development
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Force JSON response header MUST be set before any output
header('Content-Type: application/json');

// Include DB connection
// NOTE: Make sure the path to db_connect.php is correct (../../includes/db_connect.php)
require_once '../../includes/db_connect.php';

// Safely decode incoming JSON
$input = json_decode(file_get_contents("php://input"), true);
$styles = $input['styles'] ?? [];
$details = []; // Initialize empty array for results

// If no styles are provided, return empty JSON immediately
if (empty($styles)) {
    echo json_encode($details);
    exit;
}

// ------------------------------------------------------------------
// 1. Setup for Prepared Statement (Security & Consistency)
// ------------------------------------------------------------------

$placeholders = [];
$types = '';
$params = [];

// Create placeholders (? for prepared statement) and build parameters
foreach ($styles as $style) {
    $placeholders[] = '?';
    $types .= 's'; // 's' for string
    $params[] = $style;
}

$styleList = implode(',', $placeholders);

// ------------------------------------------------------------------
// 2. Build Query - FIX: Added s.id AS supplier_id and adjusted p.style alias
// ------------------------------------------------------------------

$query = "
    SELECT 
        p.id AS sno, 
        p.style,             -- CRITICAL FIX: Removed AS buyer_style so client JS can use row.style
        p.description, 
        p.department, 
        p.size_range, 
        p.qty, 
        p.currency, 
        p.target,
        s.id AS supplier_id, -- ADDITION: Required for the client-side price saving logic
        s.supplier_name
    FROM products p
    LEFT JOIN suppliers s ON p.id = s.product_id
    WHERE p.style IN ($styleList)
    ORDER BY p.style, s.supplier_name
";

// Prepare the statement
$stmt = $conn->prepare($query);

if ($stmt === false) {
    // Log fatal error if statement preparation fails
    error_log("Prepare failed: " . $conn->error);
    echo json_encode([]); 
    exit;
}

// Dynamically bind parameters
$bind_params = array_merge([$types], $params);
// PHP 5.6+ supports argument unpacking with '...'
$stmt->bind_param(...$bind_params);

// Execute query and fetch results
if ($stmt->execute()) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $details[] = $row;
    }
    $result->free();
} else {
    error_log("Execute failed: " . $stmt->error);
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Return final JSON
echo json_encode($details);

// CRITICAL FIX: Terminate script execution immediately after outputting JSON
exit;
