<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../../includes/db_connect.php';

// Optional search by buyer
$buyer = isset($_GET['buyer']) ? trim($_GET['buyer']) : '';
$searchClause = '';
if (!empty($buyer)) {
  $safeBuyer = $conn->real_escape_string($buyer);
  $searchClause = "WHERE buyer LIKE '%$safeBuyer%'";
}

$sql = "SELECT * FROM products $searchClause ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Inquiry Details</title>
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
    form {
      text-align: center;
      margin-bottom: 20px;
    }
    input[type="text"] {
      padding: 8px;
      width: 200px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    button {
      padding: 8px 16px;
      background-color: #00796b;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      margin-left: 10px;
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

<h2>Inquiry Details</h2>

<form method="GET" action="">
  <input type="text" name="buyer" placeholder="Search by Buyer" value="<?php echo htmlspecialchars($buyer); ?>">
  <button type="submit">Search</button>
</form>

<?php if ($result && $result->num_rows > 0): ?>
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
  <p style="text-align:center;">No inquiry records found.</p>
<?php endif; ?>

<?php $conn->close(); ?>
</body>
</html>