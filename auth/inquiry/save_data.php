<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  session_start();
  require_once __DIR__ . '/../../includes/db_connect.php';


  // Collect form data
  $sno = $_POST['sno'];
  $buyer = $_POST['buyer'];
  $style = $_POST['style'];
  $description = $_POST['description'];
  $department = $_POST['department'];
  $size_range = $_POST['size_range'];
  $intake = $_POST['intake'];
  $season = $_POST['season'];
  $fabric = $_POST['fabric'];
  $gsm = $_POST['gsm'];
  $composition = $_POST['composition'];
  $qty = $_POST['qty'];
  $target = $_POST['target'];
  $currency = $_POST['currency'];
  $suppliers = $_POST['suppliers'];

  // Handle image upload
 $imagePath = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
  $imageName = basename($_FILES['image']['name']);
  $targetDir = 'uploads/';
  if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
  }

  $imagePath = $targetDir . time() . '_' . $imageName;
  $sourcePath = $_FILES['image']['tmp_name'];
  $imageType = mime_content_type($sourcePath);

  // Resize and compress
  list($width, $height) = getimagesize($sourcePath);
  $newWidth = 300; // adjust as needed
  $newHeight = intval($height * ($newWidth / $width));

  $thumb = imagecreatetruecolor($newWidth, $newHeight);

  if ($imageType === 'image/jpeg') {
    $source = imagecreatefromjpeg($sourcePath);
    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    imagejpeg($thumb, $imagePath, 60); // 60% quality
  } elseif ($imageType === 'image/png') {
    $source = imagecreatefrompng($sourcePath);
    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    imagepng($thumb, $imagePath, 6); // compression level 0–9
  } else {
    move_uploaded_file($sourcePath, $imagePath); // fallback
  }

  imagedestroy($thumb);
}

  // Insert product data
  $stmt = $conn->prepare("INSERT INTO products (sno, buyer, image_path, style, description, department, size_range, intake, season, fabric, gsm, composition, qty, target, currency) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("isssssssssssiis", $sno, $buyer, $imagePath, $style, $description, $department, $size_range, $intake, $season, $fabric, $gsm, $composition, $qty,$target, $currency);
  $stmt->execute();
  $product_id = $stmt->insert_id;
  $stmt->close();

  // Insert supplier data
  foreach ($suppliers as $supplier) {
    $supplier = trim($supplier);
    if ($supplier !== '') {
      $stmt = $conn->prepare("INSERT INTO suppliers (product_id, supplier_name) VALUES (?, ?)");
      $stmt->bind_param("is", $product_id, $supplier);
      $stmt->execute();
      $stmt->close();
    }
  }

  // Close connection
  $conn->close();

  // Confirmation
  echo "<h3>✅ Data Saved Successfully!</h3>";
  echo "<p><strong>Suppliers:</strong> " . implode(', ', array_filter($suppliers)) . "</p>";
  echo "<p><a href='input_form.php'>🔙 Go Back</a></p>";
}
?>