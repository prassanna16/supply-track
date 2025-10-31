<?php
include '../includes/db_connect.php';

$id = $_GET['id'] ?? '';
$sql = "SELECT * FROM inquiries WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  echo json_encode(["success" => true, "inquiry" => $row]);
} else {
  echo json_encode(["success" => false]);
}
?>