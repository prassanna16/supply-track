<?php
// Show errors during development
ini_set('display_errors', 1);
error_reporting(E_ALL);

// CRITICAL: Force JSON response header MUST be set before any output
header('Content-Type: application/json');

// Include DB connection
// NOTE: Adjust path if needed (e.g., if this file is in inquiry/ and db_connect is in includes/)
require_once '../../includes/db_connect.php'; 

$styles = []; // Initialize empty array for results

// Use Object-Oriented MySQLi for consistency
$query = "SELECT DISTINCT style FROM products ORDER BY style ASC";

// Execute query using the object-oriented method
$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // --- Correction for Robustness ---
        // Trim whitespace and ensure only non-empty strings are added.
        $style = trim($row['style']);
        if (!empty($style)) {
            $styles[] = $style;
        }
        // ----------------------------------
    }
    // Free result set
    $result->free(); 
} else {
    // Optional: Log error for debugging if the query fails
    error_log("Query failed in get_styles.php: " . $conn->error);
    // If the query fails, $styles remains an empty array, which is fine for JSON output.
}

// Close the connection (optional here but good practice)
$conn->close();

// Return final JSON
echo json_encode($styles);

// CRITICAL: Terminate script execution immediately after outputting JSON
exit;
