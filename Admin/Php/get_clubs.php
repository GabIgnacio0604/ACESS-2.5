<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../Accounts/db_connection.php';

// Check connection
if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit;
}

// Fetch all clubs from the database
$query = "SELECT id, club_name, adviser, group_chat, description FROM clubs ORDER BY club_name ASC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $clubs = [];
    while ($row = $result->fetch_assoc()) {
        $clubs[] = $row;
    }
    echo json_encode([
        'success' => true,
        'clubs' => $clubs
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No clubs found.'
    ]);
}

$conn->close();
?>