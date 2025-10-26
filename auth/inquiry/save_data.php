<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  require_once __DIR__ . '/../../includes/db_connect.php';
  require_once __DIR__ . '/../../vendor/autoload.php';
  use Ilovepdf\Ilovepdf;

  $total = count($_POST['buyer']);

  for ($i = 0; $i < $total; $i++) {
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
    $pdfPath = '';

    // ✅ Handle image upload
    if (isset($_FILES['image']['name'][$i]) && $_FILES['image']['error'][$i] === UPLOAD_ERR_OK) {
      $imageName = basename($_FILES['image']['name'][$i]);
      $targetDir = __DIR__ . '/uploads/';
      if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
      }
      $uniqueName = time() . '_' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $imageName);
      $fullPath = $targetDir . $uniqueName;

      if (is_uploaded_file($_FILES['image']['tmp_name'][$i])) {
        if (move_uploaded_file($_FILES['image']['tmp_name'][$i], $fullPath)) {
          $imagePath = $uniqueName;
        }
      }
    }

    // ✅ Handle PDF upload + compression using iLovePDF
    if (isset($_FILES['pdf']['name'][$i]) && $_FILES['pdf']['error'][$i] === UPLOAD_ERR_OK) {
      $pdfName = basename($_FILES['pdf']['name'][$i]);
      $targetDir = __DIR__ . '/doc/';
      if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
      }
      $uniquePDF = time() . '_' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $pdfName);
      $fullPDFPath = $targetDir . $uniquePDF;

      if (is_uploaded_file($_FILES['pdf']['tmp_name'][$i])) {
        if (move_uploaded_file($_FILES['pdf']['tmp_name'][$i], $fullPDFPath)) {
          try {
            $ilovepdf = new Ilovepdf('project_public_91ffdeeb6179bd368e518196704713c3_51BdU8098a700c4b2d0623f85c0449a5a6a0c', 'secret_key_d0f0b1ab038531be6c8c29b73c3bb598_a0vrafc94b846085760789a2a7342f2de4869'); // Replace with your actual keys
            $task = $ilovepdf->newTask('compress');
            $task->addFile($fullPDFPath);
            $task->execute();
            $task->download($targetDir);

            $compressedPath = $targetDir . $uniquePDF;
            if (file_exists($compressedPath)) {
              if (filesize($compressedPath) <= 1024 * 1024) {
                $pdfPath = $uniquePDF;
              } else {
                unlink($compressedPath); // discard if too large
              }
            }
          } catch (Exception $e) {
            error_log("PDF compression failed: " . $e->getMessage());
            $pdfPath = $uniquePDF; // fallback to original
          }
        }
      }
    }

    // ✅ Insert product
    $stmt = $conn->prepare("INSERT INTO products (
      buyer, style, description, department, size_range, intake, season, fabric, gsm, composition, qty, target, currency, image_path, pdf_path
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssdiisss", $buyer, $style, $description, $department, $size_range, $intake, $season, $fabric, $gsm, $composition, $qty, $target, $currency, $imagePath, $pdfPath);
    $stmt->execute();
    $product_id = $stmt->insert_id;
    $stmt->close();

    // ✅ Insert suppliers
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
      window.location.href = "inquiries_new.html";
    }, 3000);
  </script>

</body>
</html>