<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  require_once __DIR__ . '/../../includes/db_connect.php';

  $total = count($_POST['buyer']);

  for ($i = 0; $i < $total; $i++) {
    // Collect product data
    $buyer = $_POST['buyer'][$i];
    $style = $_POST['style'][$i];
    $description = $_POST['description'][$i];
    $department = $_POST['department'][$i];
    $size_range = $_POST['size_range'][$i];
    $intake = $_POST['intake'][$i];
    $season = $_POST['season'][$i];
    $fabric = $_POST['fabric'][$i];
    $gsm = $_POST['gsm'][$i];
    $composition = $_POST['composition'][$i];
    $qty = $_POST['qty'][$i];
    $target = $_POST['target'][$i];
    $currency = $_POST['currency'][$i];
    $imagePath = '';

    // Handle image upload
    if (isset($_FILES['image']['name'][$i]) && $_FILES['image']['error'][$i] === UPLOAD_ERR_OK) {
      $imageName = basename($_FILES['image']['name'][$i]);
      $targetDir = 'uploads/';
      if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
      }
      $imagePath = $targetDir . time() . '_' . $imageName;
      move_uploaded_file($_FILES['image']['tmp_name'][$i], $imagePath);
    }

    // Insert product
    $stmt = $conn->prepare("INSERT INTO products (buyer, style, description, department, size_range, intake, season, fabric, gsm, composition, qty, target, currency, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssiis", $buyer, $style, $description, $department, $size_range, $intake, $season, $fabric, $gsm, $composition, $qty, $target, $currency, $imagePath);
    $stmt->execute();
    $product_id = $stmt->insert_id;
    $stmt->close();

    // Insert suppliers
    if (!empty($_POST['suppliers'][$i])) {
      foreach ($_POST['suppliers'][$i] as $supplier) {
        $supplier = trim($supplier);
        if ($supplier !== '') {
          $stmt = $conn->prepare("INSERT INTO suppliers (product_id, supplier_name) VALUES (?, ?)");
          $stmt->bind_param("is", $product_id, $supplier);
          $stmt->execute();
          $stmt->close();
        }
      }
    }
  }

  $conn->close();
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Saved</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0fdfc;
      text-align: center;
      padding: 60px;
    }
    .toast {
      display: inline-block;
      background-color: #4caf50;
      color: white;
      padding: 15px 25px;
      border-radius: 8px;
      font-size: 18px;
      animation: fadein 0.5s, fadeout 0.5s 2.5s;
    }
    @keyframes fadein { from {opacity: 0;} to {opacity: 1;} }
    @keyframes fadeout { from {opacity: 1;} to {opacity: 0;} }
    .btn {
      margin-top: 40px;
      padding: 12px 24px;
      background-color: #00796b;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
      text-decoration: none;
    }
    .btn:hover {
      background-color: #004d40;
    }
  </style>
</head>
<body>

  <div class="toast">✅ All entries saved successfully!</div>
  <br><br>
  <a href="dashboard.php" class="btn">Go to Dashboard</a>

  <script>
    setTimeout(function() {
      window.location.href = "inquiries_new.html"; // Adjust if needed
    }, 3000);
  </script>

</body>
</html>