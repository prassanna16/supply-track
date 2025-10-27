<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Access control
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login_admin.html");
    exit;
}

require_once '../includes/db_connect.php'; // Adjusted for auth/ folder
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin';

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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - SupplyTrack</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background-color: #f5f5f5;
    }
    .top-bar {
      background-color: #FF0000;
      color: white;
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .username-dropdown {
      position: relative;
      display: inline-block;
    }
    .username-btn {
      background: none;
      border: none;
      color: white;
      font-weight: bold;
      cursor: pointer;
    }
    .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      background-color: white;
      min-width: 100px;
      box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
      z-index: 1;
    }
    .dropdown-content a {
      color: black;
      padding: 8px 12px;
      text-decoration: none;
      display: block;
    }
    .dropdown-content a:hover {
      background-color: #ddd;
    }
    .username-dropdown:hover .dropdown-content {
      display: block;
    }
    .section {
      background-color: white;
      margin: 20px;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .section h2 {
      margin-top: 0;
      color: #00796b;
      cursor: pointer;
    }
    .btn-group {
      display: flex;
      gap: 15px;
      margin: 10px 0;
      flex-wrap: wrap;
    }
    .btn {
      background-color: #00bcd4;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      text-decoration: none;
      font-weight: bold;
    }
    .btn:hover {
      background-color: #0097a7;
    }
    .content {
      display: none;
      margin-top: 10px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
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
    form {
      margin-top: 20px;
      text-align: center;
    }
    input[type="text"] {
      padding: 8px;
      width: 200px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    button[type="submit"] {
      padding: 8px 16px;
      background-color: #00796b;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      margin-left: 10px;
    }
  </style>
  <script>
    function toggleContent(id) {
      const content = document.getElementById(id);
      content.style.display = content.style.display === 'none' ? 'block' : 'none';
    }
  </script>
</head>
<body>

<div class="top-bar">
  <div><strong>Admin Dashboard</strong></div>
  <div class="username-dropdown">
    <button class="username-btn"><?php echo $username; ?> ▼</button>
    <div class="dropdown-content">
      <a href="logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="section">
  <h2 onclick="toggleContent('inquiries')">INQUIRIES ▼</h2>
  <div class="content" id="inquiries">
    <div class="btn-group">
      <a href="inquiry/inquiries_new.html" class="btn">New Entry</a>
      <a href="inquiry/inquiries_details.php" class="btn">Details</a>
    </div>
  </div>
</div>

<div class="section">
  <h2 onclick="toggleContent('orders')">ORDERS ▼</h2>
  <div class="content" id="orders">
    <div class="btn-group">
      <a href="../orders/orders_new.php" class="btn">New Entry</a>
      <a href="../orders/orders_details.php" class="btn">Details</a>
    </div>
  </div>
</div>

<div class="section">
  <h2 onclick="toggleContent('suppliers')">SUPPLIERS ▼</h2>
  <div class="content" id="suppliers">
    <div class="btn-group">
      <a href="../suppliers/suppliers_new.php" class="btn">New Entry</a>
      <a href="../suppliers/suppliers_data.php" class="btn">DATA</a>
    </div>
  </div>
</div>

<div class="section">
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
          <td><?php echo htmlspecialchars
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
              $imagePath = "inquiry/uploads/" . $imageFile;
              if (!empty($imageFile) && file_exists(__DIR__ . "/inquiry/uploads/" . $imageFile)) {
                echo "<img src='$imagePath' class='product-image' alt='Product Image'>";
              } else {
                echo 'No image';
              }
            ?>
          </td>
          <td>
            <?php
              $pdfFile = $row['pdf_path'] ?? '';
              $pdfPath = "inquiry/doc/" . $pdfFile;
              if (!empty($pdfFile) && file_exists(__DIR__ . "/inquiry/doc/" . $pdfFile)) {
                echo "<a href='$pdfPath' download title='Download PDF'>
                        <img src='../assets/image/pdf_icon.png' alt='Download PDF' style='width:24px;height:auto;'>
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
</div>

<?php $conn->close(); ?>
</body>
</html>