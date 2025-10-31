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
/* styles.css */
body {
  height: 100%;
  margin: 0;
  overflow: hidden;
  font-family: 'Segoe UI', sans-serif;
  background-color: #f9f9f9;
}

body.modal-open {
  overflow: hidden;
}

.main {
  height: calc(100vh - 140px);
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.content-wrapper {
  width: 100%;
  max-width: 1500px;
  padding: 30px;
}

.header-bar, .top-nav {
  background-color: #fff;
  padding: 10px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  z-index: 10;
}

.logo {
  height: 50px;
  border-radius: 12px;
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
  border-radius: 6px;
  z-index: 1;
}

.username-dropdown:hover .dropdown-content {
  display: block;
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
}

.arrow.rotate {
  transform: rotate(180deg);
}

.btn-group {
  position: absolute;
  top: calc(100% + 12px);
  left: 0;
  background-color: #f7f2f2;
  border-radius: 12px;
  padding: 12px;
  box-shadow: 0 6px 12px rgba(0,0,0,0.3);
  display: none;
  flex-direction: column;
  gap: 10px;
  z-index: 9999;
}

.btn-group.show {
  display: flex;
}

.btn {
  background-color: #f6eded;
  color: #0a0101;
  border: none;
  padding: 10px 12px;
  border-radius: 12px;
  font-weight: bold;
  cursor: pointer;
}

.modal {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
  z-index: 9999;
  display: none;
  padding: 40px 20px;
  box-sizing: border-box;
}

.modal-content {
  background: #fff;
  margin: auto;
  padding: 20px 30px 60px 30px;
  border-radius: 12px;
  width: 95%;
  max-width: 1400px;
  max-height: 100vh;
  overflow-y: auto;
}

.close {
  position: absolute;
  top: 10px;
  right: 20px;
  font-size: 30px;
  font-weight: bold;
  cursor: pointer;
  color: #B22222;
}

.multi-select-wrapper {
  position: relative;
  min-width: 150px;
}

.multi-select-toggle {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #fff;
}

.multi-select-dropdown {
  display: none;
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  max-height: 200px;
  overflow-y: auto;
  border: 1px solid #ccc;
  background-color: #fff;
  z-index: 100;
  border-radius: 6px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.multi-select-dropdown.visible {
  display: block;
}

.multi-select-dropdown label {
  display: block;
  padding: 4px 8px;
}

.product-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
  display: block;
  overflow-x: auto;
  white-space: nowrap;
}

.product-table th, .product-table td {
  border: 1px solid #ccc;
  padding: 6px 4px;
  text-align: center;
  font-size: 0.85em;
}

.supplier-price {
  width: 80px;
  padding: 4px;
  border-radius: 4px;
  border: 1px solid #B22222;
  text-align: center;
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
    <h2>Product Details</h2>

    <div id="styleModal" class="modal">
      <div class="modal-content">
        <span class="close" onclick="closeStyleModal()">&times;</span>
        <h2>Select Styles for Pricing</h2>
        <div class="multi-select-wrapper">
          <div class="multi-select-toggle" onclick="toggleDropdown()">
            <span id="selectedStyles">Select style</span>
            <span class="arrow">&#9662;</span>
          </div>
          <div id="styleDropdown" class="multi-select-dropdown"></div>
        </div>
        <div id="productDetailsContainer"></div>
        <button type="button" class="btn" style="background-color: #28a745; color: white;">Save Prices</button>
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
            <td><?= $sno++ ?></td>
            <td><?= htmlspecialchars($row['buyer']) ?></td>
            <td><?= htmlspecialchars($row['style']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td><?= htmlspecialchars($row['department']) ?></td>
            <td><?= htmlspecialchars($row['size_range']) ?></td>
            <td><?= htmlspecialchars($row['qty']) ?></td>
            <td><?= htmlspecialchars($row['currency']) ?></td>
            <td><?= htmlspecialchars($row['target']) ?></td>
            <td><?= isset($supplierMap[$row['id']]) ? implode(', ', array_map('htmlspecialchars', $supplierMap[$row['id']])) : '—' ?></td>
            <td>
              <?php
                $img = $row['image_path'];
                $path = "inquiry/uploads/$img";
                echo (!empty($img) && file_exists(__DIR__ . "/$path")) ? "<img src='$path' class='product-image'>" : 'No image';
              ?>
            </td>
            <td>
              <?php
                $pdf = $row['pdf_path'];
                $pdfPath = "inquiry/doc/$pdf";
                echo (!empty($pdf) && file_exists(__DIR__ . "/$pdfPath")) ?
                  "<a href='$pdfPath' download title='Download PDF'>
                    <img src='../assets/image/pdf_icon.png' alt='Download PDF' style='width:24px;height:auto;'>
                  </a>" : 'No PDF';
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
let modalStyleDropdownTimeout = null;
let cachedStyles = null;

/** Top navigation dropdown toggle */
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

/** Outside click handler for nav dropdowns */
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

/** Outside click handler for modal dropdown */
function handleModalOutsideClick(event) {
  const styleSelectBox = document.getElementById('styleSelectBox');
  const styleDropdown = document.getElementById('styleDropdown');
  if (styleDropdown.classList.contains('visible') && !styleSelectBox.contains(event.target)) {
    styleDropdown.classList.remove('visible');
  }
}

['click', 'touchstart'].forEach(evt => {
  document.addEventListener(evt, handleOutsideClick);
  document.addEventListener(evt, handleModalOutsideClick);
});

/** Modal open */
function showStylePanel() {
  document.getElementById('styleModal').style.display = 'block';
  document.body.classList.add('modal-open');
  loadStyles();
}

/** Modal close */
function closeStyleModal() {
  document.getElementById('styleModal').style.display = 'none';
  document.body.classList.remove('modal-open');
  document.getElementById('styleDropdown').classList.remove('visible');
}

/** Toggle style dropdown */
function toggleDropdown() {
  const dropdown = document.getElementById('styleDropdown');
  dropdown.classList.toggle('visible');
  if (dropdown.classList.contains('visible')) {
    clearTimeout(modalStyleDropdownTimeout);
  }
}

/** Load styles from server */
function loadStyles() {
  if (cachedStyles) {
    renderStyles(cachedStyles);
    return;
  }

  fetch('inquiry/get_styles.php')
    .then(res => {
      const contentType = res.headers.get("content-type");
      if (res.status !== 200 || !contentType.includes("application/json")) {
        return res.text().then(text => {
          console.error('Non-JSON Response from get_styles.php:', text);
          throw new Error("Server error: get_styles.php returned non-JSON content.");
        });
      }
      return res.json();
    })
    .then(styles => {
      if (!Array.isArray(styles)) throw new Error("Invalid data format: expected array.");
      cachedStyles = styles;
      renderStyles(styles);
    })
    .catch(error => {
      console.error('Error loading styles:', error);
      document.getElementById('styleDropdown').innerHTML = `<p style="color:red; padding: 10px;">Error loading styles. Check console.</p>`;
    });
}

/** Render styles into dropdown */
function renderStyles(styles) {
  const dropdown = document.getElementById('styleDropdown');
  dropdown.innerHTML = '';
  styles.forEach(style => {
    const label = document.createElement('label');
    label.innerHTML = `
      <input type="checkbox" value="${style}" onchange="updateSelectedStyles(true)" />
      ${style}
    `;
    dropdown.appendChild(label);
  });
}

/** Update selected styles and optionally auto-close dropdown */
function updateSelectedStyles(closeAfterSelection = false) {
  const checkboxes = document.querySelectorAll('#styleDropdown input[type="checkbox"]');
  const selected = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
  const display = selected.length > 0 ? selected.join(', ') : 'Select style';
  document.getElementById('selectedStyles').textContent = display;

  if (selected.length > 0) {
    loadProductDetails(selected);
  } else {
    document.getElementById('productDetailsContainer').innerHTML = '';
  }

  if (closeAfterSelection) {
    clearTimeout(modalStyleDropdownTimeout);
    modalStyleDropdownTimeout = setTimeout(() => {
      document.getElementById('styleDropdown').classList.remove('visible');
    }, 3000);
  }
}

/** Load product details for selected styles */
function loadProductDetails(selectedStyles) {
  fetch('inquiry/get_product_details.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ styles: selectedStyles })
  })
    .then(res => {
      const contentType = res.headers.get("content-type");
      if (res.status !== 200 || !contentType.includes("application/json")) {
        return res.text().then(text => {
          console.error('Non-JSON Response from get_product_details.php:', text);
          throw new Error("Server error: get_product_details.php returned non-JSON content.");
        });
      }
      return res.json();
    })
    .then(data => renderProductDetails(data))
    .catch(error => {
      console.error('Error fetching product details:', error);
      document.getElementById('productDetailsContainer').innerHTML =
        `<p style="color:red;">Failed to load product details. ${error.message}</p>`;
    });
}

/** Render product details into modal */
function renderProductDetails(data) {
  const container = document.getElementById('productDetailsContainer');
  container.innerHTML = '';

  if (!data || data.length === 0) {
    container.innerHTML = '<p>No product details found for selected styles.</p>';
    return;
  }

  const grouped = {};
  data.forEach(row => {
    const key = row.style;
    if (!grouped[key]) grouped[key] = [];
    grouped[key].push(row);
  });

  Object.keys(grouped).forEach(style => {
    const rows = grouped[style];
    const base = rows[0];
    const section = document.createElement('div');
    section.className = 'product-block';

    const supplierHeaders = rows.map((r, i) => `<th>Supplier ${i + 1}<br>(${r.supplier_name || '-'})</th>`).join('');
    const priceInputs = rows.map(r => `
      <td>
        <input type="text" class="supplier-price"
               data-style="${style}"
               data-supplier-id="${r.supplier_id}"
               placeholder="Price" />
      </td>
    `).join('');

    section.innerHTML = `
      <h3>Style: ${style} - Buyer: ${base.buyer}</h3>
      <table class="product-table">
        <thead>
          <tr>
            <th>Description</th>
            <th>Department</th>
            <th>Size Range</th>
            <th>QTY</th>
            <th>Target</th>
            ${supplierHeaders}
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>${base.description || '-'}</td>
            <td>${base.department || '-'}</td>
            <td>${base.size_range || '-'}</td>
            <td>${base.qty || '-'}</td>
            <td>${base.currency || ''} ${base.target || '-'}</td>
            ${rows.map(r => `<td>${r.supplier_name || '-'}</td>`).join('')}
          </tr>
          <tr>
            <td><strong>Enter Supplier Prices:</strong></td>
            <td></td><td></td><td></td><td></td>
            ${priceInputs}
          </tr>
        </tbody>
      </table>
    `;
    container.appendChild(section);
  });
}
</script>
<?php $conn->close(); ?>
</body>
</html>