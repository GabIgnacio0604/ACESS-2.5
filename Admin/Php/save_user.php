<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "u465284186_ACESS";
$pass = "Acess12345";
$db   = "u465284186_ACESS";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$email = isset($data['email']) ? $conn->real_escape_string($data['email']) : '';
$role  = isset($data['role']) && !empty($data['role']) ? $conn->real_escape_string($data['role']) : 'Student';
$fullname = isset($data['fullname']) ? $conn->real_escape_string($data['fullname']) : '';
$password  = isset($data['password']) && !empty($data['password']) ? $conn->real_escape_string($data['password']) : '';
$school_id = isset($data['school_id']) ? $conn->real_escape_string($data['school_id']) : '';

if (empty($email)) {
    echo json_encode(["success" => false, "message" => "Missing email"]);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$query = "UPDATE users SET status='active', role='$role', fullname='$fullname', password='$hashedPassword', lrn='$school_id' WHERE email='$email'";

if ($conn->query($query)) {
    echo json_encode(["success" => true, "message" => "User saved successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to save details"]);
}

$conn->close();
?>