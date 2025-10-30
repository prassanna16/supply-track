<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login_admin.html");
    exit;
}

// Ensure the path to db_connect.php is correct based on your file structure
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
        /* ------------------------------------------------------------------ */
        /* GLOBAL / LAYOUT STYLES */
        /* ------------------------------------------------------------------ */
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9f9f9;
        }
        
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
        
        h2 {
            color: #B22222;
            margin-bottom: 10px;
        }
        
        /* ------------------------------------------------------------------ */
        /* HEADER BAR (omitted for brevity) */
        /* ------------------------------------------------------------------ */
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
        
        /* Admin dropdown (omitted for brevity) */
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
        
        /* ------------------------------------------------------------------ */
        /* TOP NAV & DROPDOWN MENU (omitted for brevity) */
        /* ------------------------------------------------------------------ */
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
        
        .nav-wrapper {
            position: relative;
            display: inline-block;
            z-index: 10;
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
        
        /* ------------------------------------------------------------------ */
        /* MODAL WINDOW - ALIGNMENT FIXES */
        /* ------------------------------------------------------------------ */
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

        /* FIX 1: Ensure multiselect dropdown opens correctly (was pushing content sideways) */
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
            /* Ensure dropdown fits its wrapper */
            box-sizing: border-box; 
        }

        /* Product table specific styles */
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            /* Key: Ensure horizontal scrolling works */
            display: block; 
            overflow-x: auto;
            white-space: nowrap;
        }

        /* FIX 2: Crucial change to force rows to be full-width */
        .product-table thead, .product-table tbody, .product-table tr {
            display: table; /* Treat thead/tbody/tr as full-width table element */
            width: 100%;
            table-layout: fixed; /* Ensures fixed-width columns stick */
        }
        
        /* FIX 3: Fixed Widths for Data Columns */
        .product-table th:nth-child(1), .product-table td:nth-child(1) { min-width: 120px; width: 120px; } /* Description */
        .product-table th:nth-child(2), .product-table td:nth-child(2) { min-width: 80px; width: 80px; } /* Department */
        .product-table th:nth-child(3), .product-table td:nth-child(3) { min-width: 90px; width: 90px; } /* Size Range */
        .product-table th:nth-child(4), .product-table td:nth-child(4) { min-width: 60px; width: 60px; } /* QTY */
        .product-table th:nth-child(5), .product-table td:nth-child(5) { min-width: 80px; width: 80px; } /* Target */
        
        /* FIX 4: Default minimum width for Supplier/Price columns (from 6th column onwards) */
        .product-table th:nth-child(n+6), .product-table td:nth-child(n+6) {
             min-width: 100px; 
             width: 100px; /* Set a specific width for supplier columns */
        }

        /* Styles for all cells */
        .product-table th, .product-table td {
            border: 1px solid #ccc;
            padding: 6px 4px; 
            text-align: center;
            font-size: 0.85em; 
            box-sizing: border-box;
            white-space: normal;
        }

        /* Style the 'Enter Supplier Prices' label cell (first TD in the last row) */
        .product-table tbody tr:last-child td:first-child {
            text-align: right;
            font-weight: bold;
        }

        /* Style the empty cells in the Price Input row (columns 2, 3, 4, 5) */
        .product-table tbody tr:last-child td:nth-child(n+2):nth-child(-n+5) {
             /* Set height and width to match a normal cell but remain visually empty */
             min-width: inherit;
             width: inherit;
             background-color: #f9f9f9; /* Light background to visually indicate they are spacers */
        }
        
        .supplier-price {
            width: 80px; 
            min-width: 80px;
            padding: 4px;
            box-sizing: border-box;
            text-align: center;
            border-radius: 4px;
            border: 1px solid #B22222;
        }
        
        /* The rest of the modal styles (omitted for brevity) */

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

        <div id="styleModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close" onclick="closeStyleModal()">&times;</span>
                <h2>Select Styles for Pricing</h2>

                <div class="style-dropdown">
                    <label for="styleSelect">Styles:</label>
                    <div id="styleSelectBox" class="multi-select-wrapper">
                        <div class="multi-select-toggle" onclick="toggleDropdown()">
                            <span id="selectedStyles">Select style</span>
                            <span class="arrow">&#9662;</span>
                        </div>
                        <div id="styleDropdown" class="multi-select-dropdown" style="display: none;"></div>
                    </div>
                </div>

                <div id="productDetailsContainerWrapper">
                    <div id="productDetailsContainer"></div>
                </div>
                
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
                                // Note: The file_exists check is relative to the script location
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
        // Dropdown toggle logic (omitted for brevity)
    }

    function handleOutsideClick(event) {
        // Outside click logic (omitted for brevity)
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

    function toggleDropdown() {
        const dropdown = document.getElementById('styleDropdown');
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    }

    /**
     * Loads available styles from the server.
     */
    function loadStyles() {
        fetch('inquiry/get_styles.php')
            .then(res => {
                const contentType = res.headers.get("content-type");
                if (res.status !== 200 || !contentType || !contentType.includes("application/json")) {
                    return res.text().then(text => {
                        console.error('Non-JSON Response from get_styles.php:', text);
                        throw new Error("Server error: get_styles.php returned non-JSON content.");
                    });
                }
                return res.json();
            })
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
            })
            .catch(error => {
                console.error('Error loading styles:', error);
                document.getElementById('styleDropdown').innerHTML = `<p style="color:red; padding: 10px;">Error loading styles. Check console.</p>`;
            });
    }

    function updateSelectedStyles() {
        const checkboxes = document.querySelectorAll('#styleDropdown input[type="checkbox"]');
        const selected = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        const display = selected.length > 0 ? selected.join(', ') : 'Select style';
        document.getElementById('selectedStyles').textContent = display;

        // Only load details if styles are selected
        if (selected.length > 0) {
            loadProductDetails(selected);
        } else {
            document.getElementById('productDetailsContainer').innerHTML = '';
        }
    }

    /**
     * Loads product details for the selected styles.
     */
    function loadProductDetails(selectedStyles) {
        console.log('Sending styles to PHP:', selectedStyles);

        fetch('inquiry/get_product_details.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ styles: selectedStyles })
        })
        .then(res => {
            const contentType = res.headers.get("content-type");
            if (res.status !== 200 || !contentType || !contentType.includes("application/json")) {
                return res.text().then(text => {
                    console.error('Non-JSON Response from get_product_details.php:', text);
                    throw new Error("Server error: get_product_details.php returned non-JSON content. See console for HTML output.");
                });
            }
            return res.json();
        })
        .then(data => renderProductDetails(data))
        .catch(error => {
            console.error('Error fetching product details:', error);
            const container = document.getElementById('productDetailsContainer');
            container.innerHTML = `<p style="color:red;">Failed to load product details. ${error.message}</p>`;
        });
    }

    function renderProductDetails(data) {
        console.log('Received product data:', data);

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

            // Dynamically create the table headers for suppliers
            const supplierHeaders = rows.map((r, i) => `<th>Supplier ${i + 1}<br>(${r.supplier_name || '-'})</th>`).join('');
            
            // Dynamically create input fields for supplier prices
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
                            
                            <td></td> 
                            <td></td> 
                            <td></td> 
                            <td></td> 
                            
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