<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$pass = "";
$db   = "acess";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$email = isset($data['email']) ? $conn->real_escape_string($data['email']) : '';

if (empty($email)) {
    echo json_encode(["success" => false, "message" => "Missing email"]);
    exit;
}

$query = "DELETE FROM users WHERE email='$email'";

if ($conn->query($query)) {
    echo json_encode(["success" => true, "message" => "User deleted successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to delete user"]);
}

$conn->close();
?>