<?php
require_once __DIR__ . '/../../includes/db_connect.php';

// ✅ Step 1: Validate database connection
if (!isset($conn) || $conn->connect_error) {
  echo "<div style='color:red;'>Database Error: " . htmlspecialchars($conn->connect_error) . "</div>";
  return;
}

// ✅ Step 2: Validate product ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
  echo "<p style='color:red;'>Invalid product ID.</p>";
  return;
}

// ✅ Step 3: Run query
$result = $conn->query("SELECT * FROM products WHERE id = $id");

// ✅ Step 4: Check if product exists
if ($result && $result->num_rows > 0) {
  $row = $result->fetch_assoc();

  // ✅ Step 5: Display safely
  echo "<p><strong>S.No:</strong> " . htmlspecialchars($row['id']) . "</p>";
  echo "<p><strong>Buyer:</strong> " . htmlspecialchars($row['buyer']) . "</p>";
  echo "<p><strong>Style:</strong> " . htmlspecialchars($row['style']) . "</p>";
  echo "<p><strong>Description:</strong> " . htmlspecialchars($row['description']) . "</p>";
  echo "<p><strong>Department:</strong> " . htmlspecialchars($row['department']) . "</p>";
  echo "<p><strong>Size Range:</strong> " . htmlspecialchars($row['size_range']) . "</p>";
  echo "<p><strong>QTY:</strong> " . htmlspecialchars($row['qty']) . "</p>";
  echo "<p><strong>Currency:</strong> " . htmlspecialchars($row['currency']) . "</p>";
  echo "<p><strong>target:</strong> " . htmlspecialchars($row['target_price']) . "</p>";
} else {
  echo "<p style='color:red;'>Product not found.</p>";
}

$conn->close();
?>