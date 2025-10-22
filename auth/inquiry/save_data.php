<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  session_start();
  require_once __DIR__ . '/../../includes/db_connect.php';

  // Loop through each product entry
  $total = count($_POST['buyer']); // assuming 'buyer' is always filled
  for ($i = 0; $i < $total; $i++) {
    // Collect form data for this row
    $sno         = $_POST['sno'][$i];
    $buyer       = $_POST['buyer'][$i];
    $style       = $_POST['style'][$i];
    $description = $_POST['description'][$i];
    $department  = $_POST['department'][$i];
    $size_range  = $_POST['size_range'][$i];
    $intake      = $_POST['intake'][$i];
    $season      = $_POST['season'][$i];
    $fabric      = $_POST['fabric'][$i];
    $gsm         = $_POST['gsm'][$i];
    $composition = $_POST['composition'][$i];
    $qty         = $_POST['qty'][$i];
    $target      = $_POST['target'][$i];
    $currency    = $_POST['currency'][$i];
    $suppliers   = $_POST['suppliers'][$i]; // array of suppliers for this product

    // Handle image upload for this row
    $imagePath = '';
    if (isset($_FILES['image']['name'][$i]) && $_FILES['image']['error'][$i] === UPLOAD_ERR_OK) {
      $imageName = basename($_FILES['image']['name'][$i]);
      $targetDir = 'uploads/';
      if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
      }

      $imagePath = $targetDir . time() . '_' . $imageName;
      $sourcePath = $_FILES['image']['tmp_name'][$i];
      $imageType = mime_content_type($sourcePath);

      list($width, $height) = getimagesize($sourcePath);
      $newWidth = 300;
      $newHeight = intval($height * ($newWidth / $width));
      $thumb = imagecreatetruecolor($newWidth, $newHeight);

      if ($imageType === 'image/jpeg') {
        $source = imagecreatefromjpeg($sourcePath);
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagejpeg($thumb, $imagePath, 60);
      } elseif ($imageType === 'image/png') {
        $source = imagecreatefrompng($sourcePath);
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagepng($thumb, $imagePath, 6);
      } else {
        move_uploaded_file($sourcePath, $imagePath);
      }

      imagedestroy($thumb);
    }

    // Insert product data
    $stmt = $conn->prepare("INSERT INTO products (sno, buyer, image_path, style, description, department, size_range, intake, season, fabric, gsm, composition, qty, target, currency) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssssssssiis", $sno, $buyer, $imagePath, $style, $description, $department, $size_range, $intake, $season, $fabric, $gsm, $composition, $qty, $target, $currency);
    $stmt->execute();
    $product_id = $stmt->insert_id;
    $stmt->close();

    // Insert supplier data for this product
    foreach ($suppliers as $supplier) {
      $supplier = trim($supplier);
      if ($supplier !== '') {
        $stmt = $conn->prepare("INSERT INTO suppliers (product_id, supplier_name) VALUES (?, ?)");
        $stmt->bind_param("is", $product_id, $supplier);
        $stmt->execute();
        $stmt->close();
      }
    }
  }

  // Close connection
  $conn->close();

  // Confirmation
  echo "<h3>✅ All Entries Saved Successfully!</h3>";
  echo "<p><a href='inquiries_new.php'>🔙 Add More</a></p>";
}
?>