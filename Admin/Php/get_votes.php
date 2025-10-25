<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../Accounts/db_connection.php';

$query = "SELECT candidate_name, votes FROM voting_candidates WHERE status='active'";
$result = mysqli_query($conn, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);
$conn->close();
?>
