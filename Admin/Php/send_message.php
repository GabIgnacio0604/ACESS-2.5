<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../Accounts/db_connection.php';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
$sender_email = $input['sender_email'] ?? '';
$receiver_email = $input['receiver_email'] ?? '';
$message = trim($input['message'] ?? '');

if (!$sender_email || !$receiver_email || !$message) {
    echo json_encode(["success" => false, "message" => "Missing data"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO messages (sender_email, receiver_email, message) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $sender_email, $receiver_email, $message);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to send"]);
}

$stmt->close();
$conn->close();
?>
