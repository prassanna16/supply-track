<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../../includes/db_connect.php'; // Adjusted path

// Optional search by buyer
$buyer = isset($_GET['buyer']) ? trim($_GET['buyer']) : '';
$searchClause = '';
if (!empty($buyer)) {
  $safeBuyer = $conn->real_escape_string($buyer);
  $searchClause = "WHERE buyer LIKE '%$safeBuyer%'";
}

// Fetch products
$sql = "SELECT * FROM products $searchClause ORDER BY id DESC";
$result = $conn->query($sql);

// Fetch suppliers grouped by product_id
$supplierMap = [];
$supplierQuery = "SELECT product_id, supplier_name FROM suppliers";
$supplierResult = $conn->query($supplierQuery);
if ($supplierResult && $supplierResult->num_rows > 0) {
  while ($row = $supplierResult->fetch_assoc()) {
    $pid = $row['product_id'];
    if (!isset($supplierMap[$pid])) {
      $supplierMap[$pid] = [];
    }
    $supplierMap[$pid][] = $row['supplier_name'];
  }
}
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
      vertical-align: top;
    }
    th {
      background-color: #00bcd4;
      color: white;
    }
    tr:nth-child(even) {
      background-color: #e0f2f1;
    }
    img.product-image {
      width: 80px;
      height: auto;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
  </style>
</head>
<body>

<h2>Product Details</h2>

<form method="GET" action="">
  <input type="text" name="buyer" placeholder="Search by Buyer" value="<?php echo htmlspecialchars($buyer ?? '', ENT_QUOTES); ?>">
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
      <th>Image</th>
        <th>PDF</th>
    </tr>
    <?php $sno = 1; while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo $sno++; ?></td>
        <td><?php echo htmlspecialchars($row['buyer'] ?? '', ENT_QUOTES); ?></td>
        <td><?php echo htmlspecialchars($row['style'] ?? '', ENT_QUOTES); ?></td>
        <td><?php echo htmlspecialchars($row['description'] ?? '', ENT_QUOTES); ?></td>
        <td><?php echo htmlspecialchars($row['department'] ?? '', ENT_QUOTES); ?></td>
        <td><?php echo htmlspecialchars($row['size_range'] ?? '', ENT_QUOTES); ?></td>
        <td><?php echo htmlspecialchars($row['qty'] ?? '', ENT_QUOTES); ?></td>
        <td><?php echo htmlspecialchars($row['currency'] ?? '', ENT_QUOTES); ?></td>
        <td><?php echo htmlspecialchars($row['target'] ?? '', ENT_QUOTES); ?></td>
        <td>
          <?php
            $pid = $row['id'] ?? 0;
            if (isset($supplierMap[$pid])) {
              echo implode(', ', array_map(fn($s) => htmlspecialchars($s, ENT_QUOTES), $supplierMap[$pid]));
            } else {
              echo '—';
            }
          ?>
        </td>
        <td>
          <?php
            $imageFile = $row['image_path'] ?? '';
            $imagePath = "uploads/" . $imageFile;
            if (!empty($imageFile) && file_exists(__DIR__ . "/uploads/" . $imageFile)) {
              echo "<img src='$imagePath' class='product-image' alt='Product Image'>";
            } else {
              echo 'No image';
            }
          ?>
        </td>
        <td>
  <?php
    $pdfFile = $row['pdf_path'] ?? '';
    $pdfPath = "doc/" . $pdfFile;
    if (!empty($pdfFile) && file_exists(__DIR__ . "/doc/" . $pdfFile)) {
      echo "<a href='$pdfPath' download title='Download PDF'>
              <img src='../../assets/image/pdf_icon.png' alt='Download PDF' style='width:24px;height:auto;'>
            </a>";
    } else {
      echo 'No PDF';
    }
  ?>
</td>
      </tr>
    <?php endwhile; ?>
  </table>
<?php else: ?>
  <p style="text-align:center;">No product records found.</p>
<?php endif; ?>

<?php $conn->close(); ?>
</body>
</html>