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

/* Header with logo and admin toggle */
.header-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 30px;
  background-color: #fff;
  position: relative;
  z-index: 10;
  flex-wrap: wrap;
}

.logo {
  height: 50px;
  border-radius: 12px;
}

/* Admin dropdown */
.username-dropdown {
  position: relative;
  display: inline-block;
}

.username-btn {
  background-color: #B22222;
  color: white;
  border: none;
  padding: 10px 16px;
  border-radius: 20px;
  font-weight: bold;
  cursor: pointer;
}

.dropdown-content {
  display: none;
  position: absolute;
  right: 0;
  top: 100%;
  background-color: white;
  box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
  z-index: 1;
  border-radius: 6px;
  overflow: hidden;
}

.dropdown-content a {
  color: black;
  padding: 10px 16px;
  text-decoration: none;
  display: block;
}

.username-dropdown:hover .dropdown-content {
  display: block;
}

/* Curved top navigation bar */
.top-nav {
  display: flex;
  align-items: flex-start;
  background-color: #fff;
  padding: 15px 30px;
  border-radius: 30px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  margin: 20px;
  gap: 30px;
  flex-wrap: wrap;
  justify-content: center;
}

.nav-wrapper {
  position: relative;
  display: inline-block;
  z-index: 10; /* ensures dropdown stays above other elements */
  overflow: visible;
}
.nav-item {
  background-color: #B22222;
  color: white;
  padding: 10px 20px;
  border-radius: 20px;
  font-weight: bold;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: background-color 0.3s ease;
}

.nav-item:hover {
  background-color: #8B1A1A;
}

.arrow {
  display: inline-block;
  transition: transform 0.3s ease;
}

.arrow.rotate {
  transform: rotate(180deg);
}

/* Dropdown panel with fade and slide animation */
.btn-group {
  opacity: 0;
  visibility: hidden;
  pointer-events: none;
  position: absolute;
  top: calc(100% + 8px); /* ensures dropdown appears below the button */
  left: 0;
  background-color: #000;
  border-radius: 12px;
  padding: 10px;
  z-index: 999;
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
  flex-direction: column;
  gap: 8px;
  min-width: 160px;
  transform: translateY(-10px);
  transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
}

.btn-group.show {
  opacity: 1;
  visibility: visible;
  pointer-events: auto;
  transform: translateY(0);
}

.btn {
  background-color: #f4ececff;
  color: #fff;
  border: none;
  padding: 10px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: bold;
  transition: background-color 0.3s ease;
}

.btn:hover {
  background-color: #333;
}
/* Main content */
.main {
  flex: 1;
  padding: 30px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.content-wrapper {
  width: 100%;
  max-width: 1200px;
}

.top-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
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

/* Product table */
table {
  width: 100%;
  border-collapse: collapse;
  overflow-x: auto;
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

/* Responsive styles */
@media (max-width: 768px) {
  .header-bar {
    flex-direction: column;
    align-items: flex-start;
    padding: 15px;
  }

  .username-dropdown {
    align-self: flex-end;
    margin-top: 10px;
  }

  .top-nav {
  overflow: visible;
  position: relative;
  z-index: 5;
}

  .nav-item {
    justify-content: center;
    font-size: 14px;
    padding: 8px 16px;
  }

  .main {
    padding: 20px;
  }

  input[type="text"] {
    width: 100%;
    margin-bottom: 10px;
  }

  button[type="submit"] {
    width: 100%;
    margin-left: 0;
  }

  table {
    display: block;
    overflow-x: auto;
    white-space: nowrap;
  }
    .nav-wrapper {
    width: 100%;
  }

  .btn-group {
    left: 0;
    right: 0;
    min-width: 100%;
    position: absolute;
    top: calc(100% + 8px);
    z-index: 999;
  }

  .btn {
    text-align: center;
    padding: 12px;
    font-size: 14px;
  }
}
</style>
</head>
<body>

<div class="header-bar">
  <img src="../assets/image/logo-avis.jpg" alt="Logo" class="logo">
  <div class="username-dropdown">
    <button class="username-btn"><?php echo htmlspecialchars($username); ?> ▼</button>
    <div class="dropdown-content">
      <a href="logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="top-nav">
  <div class="nav-wrapper">
    <div class="nav-item" onclick="toggleSection('inquiriesGroup', 'arrow1')">
      INQUIRIES <span id="arrow1" class="arrow">▼</span>
    </div>
    <div class="btn-group" id="inquiriesGroup">
      <a href="inquiry/inquiries_new.html" class="btn">New Entry</a>
      <a href="inquiry/inquiries_details.php" class="btn">Details</a>
    </div>
  </div>

  <div class="nav-wrapper">
    <div class="nav-item" onclick="toggleSection('ordersGroup', 'arrow2')">
      ORDERS <span id="arrow2" class="arrow">▼</span>
    </div>
    <div class="btn-group" id="ordersGroup">
      <a href="../orders/orders_new.php" class="btn">New Entry</a>
      <a href="../orders/orders_details.php" class="btn">Details</a>
    </div>
  </div>

  <div class="nav-wrapper">
    <div class="nav-item" onclick="toggleSection('suppliersGroup', 'arrow3')">
      SUPPLIERS <span id="arrow3" class="arrow">▼</span>
    </div>
    <div class="btn-group" id="suppliersGroup">
      <a href="../suppliers/suppliers_new.php" class="btn">New Entry</a>
      <a href="../suppliers/suppliers_data.php" class="btn">DATA</a>
    </div>
  </div>
</div>

  <div class="main">
  <div class="content-wrapper">
    <div class="top-bar">
      <h2>Product Details</h2>
      <!-- Username dropdown -->
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
  let activeDropdown = null;
  let activeArrow = null;
  let hideTimeout = null;

  function toggleSection(id, arrowId) {
    const group = document.getElementById(id);
    const arrow = document.getElementById(arrowId);
    const isOpen = group.classList.contains('show');

    // Close all dropdowns
    document.querySelectorAll('.btn-group').forEach(g => g.classList.remove('show'));
    document.querySelectorAll('.arrow').forEach(a => a.classList.remove('rotate'));
    clearTimeout(hideTimeout);

    if (!isOpen) {
      group.classList.add('show');
      arrow.classList.add('rotate');
      activeDropdown = group;
      activeArrow = arrow;

      // Auto-hide after 5 seconds
      hideTimeout = setTimeout(() => {
        group.classList.remove('show');
        arrow.classList.remove('rotate');
        activeDropdown = null;
        activeArrow = null;
      }, 5000);
    } else {
      // Toggle off if already open
      activeDropdown = null;
      activeArrow = null;
    }
  }

  // Close dropdowns on outside click or mobile tap
  function handleOutsideClick(event) {
    const isNavItem = event.target.closest('.nav-item');
    const isBtnGroup = event.target.closest('.btn-group');

    if (!isNavItem && !isBtnGroup && activeDropdown) {
      activeDropdown.classList.remove('show');
      if (activeArrow) activeArrow.classList.remove('rotate');
      activeDropdown = null;
      activeArrow = null;
      clearTimeout(hideTimeout);
    }
  }

  document.addEventListener('click', handleOutsideClick);
  document.addEventListener('touchstart', handleOutsideClick);
</script>

<?php $conn->close(); ?>
</body>
</html>