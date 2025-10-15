<?php
session_start();

// Access control
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login_admin.html");
    exit;
}

$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Panel - SupplyTrack</title>
  <link rel="stylesheet" href="../assets/css/admin-login.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background-color: #f5f5f5;
    }
    .top-bar {
      background-color: #FF0000;
      color: white;
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .username-dropdown {
      position: relative;
      display: inline-block;
    }
    .username-btn {
      background: none;
      border: none;
      color: white;
      font-weight: bold;
      cursor: pointer;
    }
    .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      background-color: white;
      min-width: 100px;
      box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
      z-index: 1;
    }
    .dropdown-content a {
      color: black;
      padding: 8px 12px;
      text-decoration: none;
      display: block;
    }
    .dropdown-content a:hover {
      background-color: #ddd;
    }
    .username-dropdown:hover .dropdown-content {
      display: block;
    }

    .section {
      margin: 30px auto;
      width: 90%;
      max-width: 600px;
      background-color: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .section h2 {
      margin-top: 0;
      color: #333;
    }
    .btn-group {
      display: flex;
      gap: 15px;
      margin-top: 10px;
    }
    .btn {
      background-color: #00bcd4;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      text-decoration: none;
      font-weight: bold;
    }
    .btn:hover {
      background-color: #0097a7;
    }
  </style>
</head>
<body>

  <div class="top-bar">
    <div><strong>Admin Panel</strong></div>
    <div class="username-dropdown">
      <button class="username-btn"><?php echo $username; ?> ▼</button>
      <div class="dropdown-content">
        <a href="logout.php">Logout</a>
      </div>
    </div>
  </div>

  <div class="section">
    <h2>INQUIRIES</h2>
    <div class="btn-group">
      <a href="../components/inquiries_new.php" class="btn">New Entry</a>
      <a href="../components/inquiries_details.php" class="btn">Details</a>
    </div>
  </div>

  <div class="section">
    <h2>ORDERS</h2>
    <div class="btn-group">
      <a href="../orders/orders_new.php" class="btn">New Entry</a>
      <a href="../orders/orders_details.php" class="btn">Details</a>
    </div>
  </div>

  <div class="section">
    <h2>SUPPLIERS</h2>
    <div class="btn-group">
      <a href="../suppliers/suppliers_new.php" class="btn">New Entry</a>
      <a href="../suppliers/suppliers_data.php" class="btn">DATA</a>
    </div>
  </div>

</body>
</html>