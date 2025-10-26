<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  require_once __DIR__ . '/../../includes/db_connect.php';

  $total = count($_POST['buyer']);

  for ($i = 0; $i < $total; $i++) {
    // Collect product data
    $buyer = $_POST['buyer'][$i] ?? '';
    $style = $_POST['style'][$i] ?? '';
    $description = $_POST['description'][$i] ?? '';
    $department = $_POST['department'][$i] ?? '';
    $size_range = $_POST['size_range'][$i] ?? '';
    $intake = $_POST['intake'][$i] ?? '';
    $season = $_POST['season'][$i] ?? '';
    $fabric = $_POST['fabric'][$i] ?? '';
    $gsm = $_POST['gsm'][$i] ?? '';
    $composition = $_POST['composition'][$i] ?? '';
    $qty = $_POST['qty'][$i] ?? 0;
    $target = $_POST['target'][$i] ?? 0.0;
    $currency = $_POST['currency'][$i] ?? '';
    $imagePath = '';

    // Handle image upload
    if (isset($_FILES['image']['name'][$i]) && $_FILES['image']['error'][$i] === UPLOAD_ERR_OK) {
      $imageName = basename($_FILES['image']['name'][$i]);
      $targetDir = __DIR__ . '/uploads/';
      if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
      }
      $uniqueName = time() . '_' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $imageName);
      $fullPath = $targetDir . $uniqueName;
      if (move_uploaded_file($_FILES['image']['tmp_name'][$i], $fullPath)) {
        $imagePath = 'uploads/' . $uniqueName;
      }
    }

    // Insert product
    $stmt = $conn->prepare("INSERT INTO products (
      buyer, style, description, department, size_range, intake, season, fabric, gsm, composition, qty, target, currency, image_path
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssdiis", $buyer, $style, $description, $department, $size_range, $intake, $season, $fabric, $gsm, $composition, $qty, $target, $currency, $imagePath);
    $stmt->execute();
    $product_id = $stmt->insert_id;
    $stmt->close();

    // Insert suppliers (multiple per product row)
    if (isset($_POST['suppliers'][$i]) && is_array($_POST['suppliers'][$i])) {
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
  echo "<h3 style='text-align:center; color:green;'>✅ All entries saved successfully!</h3>";
}
?>