<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login_admin.html");
    exit;
}

require_once '../includes/db_connect.php';
$username = $_SESSION['username'] ?? 'Admin';

$buyer = $_GET['buyer'] ?? '';
$searchClause = !empty($buyer) ? "WHERE buyer LIKE '%" . $conn->real_escape_string($buyer) . "%'" : '';
$result = $conn->query("SELECT * FROM products $searchClause ORDER BY id DESC");

$supplierMap = [];
$supplierResult = $conn->query("SELECT product_id, supplier_name FROM suppliers");
while ($row = $supplierResult->fetch_assoc()) {
    $supplierMap[$row['product_id']][] = $row['supplier_name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard - SupplyTrack</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f9f9f9;
    }
    .container {
      display: flex;
      height: 100vh;
    }
    .sidebar {
      width: 250px;
      background-color: #fff;
      color: #000;
      padding: 20px;
      box-sizing: border-box;
      border-right: 1px solid #ccc;
    }
    .sidebar img {
  width: 320px;
  max-width: 100%;
  height: auto;
  margin-bottom: 20px;
    }

    .sidebar h2 {
      font-size: 18px;
      margin-top: 20px;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: #000;
    }
    .arrow {
      display: inline-block;
      transition: transform 0.3s ease;
    }
    .arrow.rotate {
      transform: rotate(180deg);
    }
    .btn-group {
      display: none;
      flex-direction: column;
      gap: 10px;
      margin-top: 10px;
      padding-left: 10px;
    }
    .btn-group.show {
      display: flex;
    }
    .btn {
      background-color: #fff;
      color: #000;
      border: 1px solid #B22222;
      padding: 10px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
    }
    .btn:hover {
      background-color: #f2f2f2;
    }
    .main {
      flex: 1;
      padding: 30px;
      overflow-y: auto;
    }
    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    .username-dropdown {
      position: relative;
      display: inline-block;
    }
    .username-btn {
      background: none;
      border: none;
      color: #333;
      font-weight: bold;
      cursor: pointer;
    }
    .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      background-color: white;
      box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
      z-index: 1;
    }
    .dropdown-content a {
      color: black;
      padding: 8px 12px;
      text-decoration: none;
      display: block;
    }
    .username-dropdown:hover .dropdown-content {
      display: block;
    }
    h2 {
      color: #B22222;
      margin-bottom: 10px;
    }
    form {
      margin-bottom: 20px;
    }
    input[type="text"] {
      padding: 8px;
      width: 200px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    button[type="submit"] {
      padding: 8px 16px;
      background-color: #B22222;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      margin-left: 10px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 10px;
      border: 1px solid #ccc;
      text-align: left;
    }
    th {
      background-color: #B22222;
      color: white;
    }
    tr:nth-child(even) {
      background-color: #f2f2f2;
    }
    img.product-image {
      width: 80px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="sidebar">
    <img src="../assets/image/logo-avis.jpg" alt="Logo">

    <h2 onclick="toggleSection('inquiriesGroup', 'arrow1')">
      INQUIRIES <span id="arrow1" class="arrow">▼</span>
    </h2>
    <div class="btn-group" id="inquiriesGroup">
      <a href="inquiry/inquiries_new.html" class="btn">New Entry</a>
      <a href="inquiry/inquiries_details.php" class="btn">Details</a>
    </div>

    <h2 onclick="toggleSection('ordersGroup', 'arrow2')">
      ORDERS <span id="arrow2" class="arrow">▼</span>
    </h2>
    <div class="btn-group" id="ordersGroup">
      <a href="../orders/orders_new.php" class="btn">New Entry</a>
      <a href="../orders/orders_details.php" class="btn">Details</a>
    </div>

    <h2 onclick="toggleSection('suppliersGroup', 'arrow3')">
      SUPPLIERS <span id="arrow3" class="arrow">▼</span>
    </h2>
    <div class="btn-group" id="suppliersGroup">
      <a href="../suppliers/suppliers_new.php" class="btn">New Entry</a>
      <a href="../suppliers/suppliers_data.php" class="btn">DATA</a>
    </div>
  </div>

  <div class="main">
    <div class="top-bar">
      <h2>Product Details</h2>
      <div class="username-dropdown">
        <button class="username-btn"><?php echo htmlspecialchars($username); ?> ▼</button>
        <div class="dropdown-content">
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </div>

    <form method="GET">
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
          <th>Image</th>
          <th>PDF</th>
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
            <td>
              <?php
                $pid = $row['id'];
                echo isset($supplierMap[$pid]) ? implode(', ', array_map('htmlspecialchars', $supplierMap[$pid])) : '—';
              ?>
            </td>
            <td>
              <?php
                $imageFile = $row['image_path'];
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
                $pdfFile = $row['pdf_path'];
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
</div>

<script>
  function toggleSection(id, arrowId) {
    const group = document.getElementById(id);
    const arrow = document.getElementById(arrowId);
    group.classList.toggle('show');
    arrow.classList.toggle('rotate');
  }
</script>

<?php $conn->close(); ?>
</body>
</html>