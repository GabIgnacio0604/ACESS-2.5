<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../Accounts/db_connection.php';

$sql = "SELECT id, date, title FROM events ORDER BY date ASC";
$result = $conn->query($sql);

if (!$result) {
  echo json_encode(["error" => "Query failed: " . $conn->error]);
  exit;
}

$events = [];
while ($row = $result->fetch_assoc()) {
  $events[] = $row;
}

echo json_encode($events);
$conn->close();
?>
