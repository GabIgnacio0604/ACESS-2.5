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
$role  = isset($data['role']) && !empty($data['role']) ? $conn->real_escape_string($data['role']) : 'Student';

if (empty($email)) {
    echo json_encode(["success" => false, "message" => "Missing email"]);
    exit;
}

$query = "UPDATE users SET status='active', role='$role' WHERE email='$email'";

if ($conn->query($query)) {
    echo json_encode(["success" => true, "message" => "User approved successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to approve user"]);
}

$conn->close();
?>
