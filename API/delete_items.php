<?php
header("Content-Type: application/json");
include_once("../Accounts/db_connection.php");

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input["id"], $input["type"])) {
  echo json_encode(["success" => false, "error" => "Missing parameters"]);
  exit;
}

$id = intval($input["id"]);
$type = $input["type"];

// Choose the correct table
$table = ($type === "event") ? "events" : (($type === "announcement") ? "club_announcements" : null);
if (!$table) {
  echo json_encode(["success" => false, "error" => "Invalid type"]);
  exit;
}

// Delete item prevent SQL injection
$stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
  echo json_encode(["success" => true]);
} else {
  echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
