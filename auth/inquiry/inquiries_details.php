<?php
session_start();
require_once '../includes/db_connect.php'; // ✅ Your reusable connection file

// Fetch product records
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Product Details</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0fdfc;
      padding: 40px;
    }
    h2 {
      text-align: center;
      color: #00796b;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 30px;
    }
    th, td {
      padding: 12px;
      border: 1px solid #ccc;
      text-align: left;
    }
    th {
      background-color: #00bcd4;
      color: white;
    }
    tr:nth-child(even) {
      background-color: #e0f2f1;
    }
  </style>
</head>
<body>

<h2>Product Details</h2>

<?php if ($result->num_rows > 0): ?>
  <table>
    <tr>
      <th>S.No</th>
      <th>Buyer</th>
      <th>Style</th>
      <th>Description</th>
      <th>Department</th>
      <th>Size Range</th>
      <th>QTY</th>
      <th>Currency</th>
      <th>Target</th>
      <th>Suppliers</th>
    </tr>
    <?php $sno = 1; while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo $sno++; ?></td>
        <td><?php echo htmlspecialchars($row['buyer']); ?></td>
        <td><?php echo htmlspecialchars($row['style']); ?></td>
        <td><?php echo htmlspecialchars($row['description']); ?></td>
        <td><?php echo htmlspecialchars($row['department']); ?></td>
        <td><?php echo htmlspecialchars($row['size_range']); ?></td>
        <td><?php echo htmlspecialchars($row['qty']); ?></td>
        <td><?php echo htmlspecialchars($row['currency']); ?></td>
        <td><?php echo htmlspecialchars($row['target']); ?></td>
        <td><?php echo htmlspecialchars($row['suppliers']); ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
<?php else: ?>
  <p>No product records found.</p>
<?php endif; ?>

<?php $conn->close(); ?>
</body>
</html>