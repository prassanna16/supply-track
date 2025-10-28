<?php
include '../../db.php';

$id = intval($_GET['id']);
$result = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
$row = mysqli_fetch_assoc($result);

echo "<p><strong>S.No:</strong> {$row['id']}</p>";
echo "<p><strong>Buyer:</strong> {$row['buyer']}</p>";
echo "<p><strong>Style:</strong> {$row['style']}</p>";
echo "<p><strong>Description:</strong> {$row['description']}</p>";
echo "<p><strong>Department:</strong> {$row['department']}</p>";
echo "<p><strong>Size Range:</strong> {$row['size_range']}</p>";
echo "<p><strong>QTY:</strong> {$row['qty']}</p>";
echo "<p><strong>Currency:</strong> {$row['currency']}</p>";
echo "<p><strong>Target:</strong> {$row['target_price']}</p>";
?>