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

/* Top navigation bar */
.top-nav {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  justify-content: center;
  padding: 15px 30px;
  background-color: #fff;
  border-radius: 30px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  margin: 20px;
  position: relative;
  z-index: 5;
  overflow: visible;
}

/* Each nav item + dropdown wrapper */
.nav-wrapper {
  position: relative;
  display: inline-block;
  z-index: 10;
}

/* Nav button */
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

/* Arrow animation */
.arrow {
  display: inline-block;
  transition: transform 0.3s ease;
}

.arrow.rotate {
  transform: rotate(180deg);
}

/* Dropdown panel */
.btn-group {
  opacity: 0;
  visibility: hidden;
  pointer-events: none;
  position: absolute;
  top: calc(100% + 12px);
  left: 0;
  background-color: #f7f2f2ff;
  border-radius: 12px;
  padding: 12px;
  z-index: 9999;
  box-shadow: 0 6px 12px rgba(0,0,0,0.3);
  display: flex;
  flex-direction: column;
  gap: 10px;
  min-width: 180px;
  transform: translateY(-10px);
  transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
}

.btn-group.show {
  opacity: 1;
  visibility: visible;
  pointer-events: auto;
  transform: translateY(0);
}

/* Dropdown buttons */
.btn {
  background-color: #f6ededff;
  color: #0a0101ff;
  border: none;
  padding: 10px 12px;
  border-radius: 12px;
  text-decoration: none;
  font-weight: bold;
  text-align: left;
  white-space: nowrap;
  transition: background-color 0.3s ease;
}

.btn:hover {
  background-color: #eeaaaaff;
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
  max-width: 1500px;
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
    flex-direction: column;
    align-items: stretch;
  }

  .nav-wrapper {
    width: 100%;
  }

  .nav-item {
    justify-content: center;
    font-size: 14px;
    padding: 8px 16px;
  }

  .btn-group {
    left: 0;
    right: 0;
    min-width: 100%;
    top: calc(100% + 12px);
  }

  .btn {
    font-size: 15px;
    padding: 16px;
    text-align: center;
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
}
.style-window {
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  padding: 20px 30px;
  margin: 30px auto;
  max-width: 600px;
  font-family: 'Segoe UI', sans-serif;
}

.style-window h2 {
  color: #B22222;
  margin-bottom: 20px;
}

.style-dropdown label {
  font-weight: bold;
  margin-right: 10px;
}

#styleSelect {
  width: 100%;
  height: 200px;
  border-radius: 6px;
  border: 1px solid #ccc;
  padding: 10px;
  font-size: 16px;
}
.modal {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
  z-index: 9999;
  display: none;
  overflow-y: auto;
  padding: 40px 20px;
  box-sizing: border-box;
}

.modal-content {
  background: #fff;
  margin: auto;
  padding: 20px 30px;
  border-radius: 12px;
  width: 100%;
  max-width: 500px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
  position: relative;
}

.close {
  float: right;
  font-size: 24px;
  font-weight: bold;
  cursor: pointer;
}
.product-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}
.product-table th, .product-table td {
  border: 1px solid #ccc;
  padding: 6px;
  text-align: center;
}
.product-block {
  margin-bottom: 20px;
}
.supplier-price {
  width: 80px;
}
.multi-select-wrapper {
  position: relative;
  width: 250px;
  font-family: sans-serif;
}

.multi-select-toggle {
  border: 1px solid #ccc;
  padding: 8px 10px;
  background-color: #fff;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-radius: 6px;
}

.multi-select-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  max-height: 200px;
  overflow-y: auto;
  border: 1px solid #ccc;
  background-color: #fff;
  z-index: 10;
  border-radius: 6px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.multi-select-dropdown label {
  display: block;
  padding: 8px 12px;
  cursor: pointer;
}

.multi-select-dropdown label:hover {
  background-color: #f5f5f5;
}

.arrow {
  font-size: 12px;
  color: #666;
}
.product-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}
.product-table th, .product-table td {
  border: 1px solid #ccc;
  padding: 6px;
  text-align: center;
}
.product-block {
  margin-bottom: 20px;
}
.supplier-price {
  width: 80px;
  padding: 4px;
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
      <button type="button" class="btn" onclick="showStylePanel()">Enter Supplier Price</button>
      <a href="inquiry/inquiries_details.php" class="btn">Details</a>
    </div>
  </div>

  <div class="nav-wrapper">
    <div class="nav-item" onclick="toggleSection('ordersGroup', 'arrow2')">
      ORDERS <span id="arrow2" class="arrow">▼</span>
    </div>
    <div class="btn-group" id="ordersGroup">
      <a href="#" class="btn">New Entry</a>
      <a href="#" class="btn">Details</a>
    </div>
  </div>

  <div class="nav-wrapper">
    <div class="nav-item" onclick="toggleSection('suppliersGroup', 'arrow3')">
      SUPPLIERS <span id="arrow3" class="arrow">▼</span>
    </div>
    <div class="btn-group" id="suppliersGroup">
      <a href="#" class="btn">New Entry</a>
      <a href="#" class="btn">Details</a>
    </div>
  </div>
</div>

<div class="main">
  <div class="content-wrapper">
    <div class="top-bar">
      <h2>Product Details</h2>
    </div>
    <!-- ✅ Modal-style popup -->
<div id="styleModal" class="modal" style="display: none;">
  <div class="modal-content">
    <span class="close" onclick="closeStyleModal()">&times;</span>
    <h2>Select Styles</h2>

    <div class="style-dropdown">
      <label for="styleSelect">Styles:</label>
      <div id="styleSelectBox" class="multi-select-wrapper">
        <div class="multi-select-toggle" onclick="toggleDropdown()">
          <span id="selectedStyles">Select style</span>
          <span class="arrow">&#9662;</span>
        </div>
        <div id="styleDropdown" class="multi-select-dropdown" style="display: none;"></div>
      </div>
    </div> <!-- ✅ Properly closed here -->

    <!-- 🔽 Product details will be injected here -->
    <div id="productDetailsContainer"></div>
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
  let activeDropdown = null;
  let activeArrow = null;
  let hideTimeout = null;

  function toggleSection(id, arrowId) {
    const group = document.getElementById(id);
    const arrow = document.getElementById(arrowId);
    const isOpen = group.classList.contains('show');

    document.querySelectorAll('.btn-group').forEach(g => g.classList.remove('show'));
    document.querySelectorAll('.arrow').forEach(a => a.classList.remove('rotate'));
    clearTimeout(hideTimeout);

    if (!isOpen) {
      group.classList.add('show');
      arrow.classList.add('rotate');
      activeDropdown = group;
      activeArrow = arrow;

      hideTimeout = setTimeout(() => {
        group.classList.remove('show');
        arrow.classList.remove('rotate');
        activeDropdown = null;
        activeArrow = null;
      }, 5000);
    } else {
      group.classList.remove('show');
      arrow.classList.remove('rotate');
      activeDropdown = null;
      activeArrow = null;
    }
  }

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

  function showStylePanel() {
    document.getElementById('styleModal').style.display = 'block';
    loadStyles();
  }

  function closeStyleModal() {
    document.getElementById('styleModal').style.display = 'none';
  }

  function loadStyles() {
    fetch('inquiry/get_styles.php')
      .then(res => res.json())
      .then(styles => {
        const dropdown = document.getElementById('styleDropdown');
        dropdown.innerHTML = '';

        styles.forEach(style => {
          const label = document.createElement('label');
          label.innerHTML = `
            <input type="checkbox" value="${style}" onchange="updateSelectedStyles()" />
            ${style}
          `;
          dropdown.appendChild(label);
        });
      });
  }

  function updateSelectedStyles() {
    const checkboxes = document.querySelectorAll('#styleDropdown input[type="checkbox"]');
    const selected = Array.from(checkboxes)
      .filter(cb => cb.checked)
      .map(cb => cb.value);

    console.log('Selected styles:', selected);

    const display = selected.length > 0 ? selected.join(', ') : 'Select style';
    document.getElementById('selectedStyles').textContent = display;

    loadProductDetails(selected);
  }
  function loadProductDetails(selectedStyles) {
    console.log('Sending styles to PHP:', selectedStyles);

    fetch('inquiry/get_product_details.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ styles: selectedStyles })
    })
    .then(res => res.json())
    .then(data => renderProductDetails(data));
  }
  function renderProductDetails(data) {
    console.log('Received product data:', data);

    const container = document.getElementById('productDetailsContainer');
    container.innerHTML = '';

    if (data.length === 0) {
      container.innerHTML = '<p>No product details found.</p>';
      return;
    }

    const grouped = {};
    data.forEach(row => {
      const key = row.buyer_style;
      if (!grouped[key]) grouped[key] = [];
      grouped[key].push(row);
    });

    Object.keys(grouped).forEach(style => {
      const rows = grouped[style];
      const base = rows[0];

      const section = document.createElement('div');
      section.className = 'product-block';

      section.innerHTML = `
        <h3>Style: ${style}</h3>
        <table class="product-table">
          <thead>
            <tr>
              <th>S.No</th><th>Buyer Style</th><th>Description</th><th>Department</th>
              <th>Size Range</th><th>QTY</th><th>Currency</th><th>Target</th>
              ${rows.map((_, i) => `<th>Supplier ${i + 1}</th>`).join('')}
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>${base.sno}</td><td>${base.buyer_style}</td><td>${base.description}</td>
              <td>${base.department}</td><td>${base.size_range}</td><td>${base.qty}</td>
              <td>${base.currency}</td><td>${base.target}</td>
              ${rows.map(r => `<td>${r.supplier_name || '-'}</td>`).join('')}
            </tr>
            <tr>
              <td colspan="8"><strong>Enter Supplier Prices:</strong></td>
              ${rows.map(r => `
                <td>
                  <input type="text" class="supplier-price"
                         data-style="${style}"
                         data-supplier="${r.supplier_name}"
                         placeholder="Price" />
                </td>
              `).join('')}
            </tr>
          </tbody>
        </table>
      `;

      container.appendChild(section);
    });
  }

  function toggleDropdown() {
    const dropdown = document.getElementById('styleDropdown');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
  }
</script>
<?php $conn->close(); ?>
</body>
</html>