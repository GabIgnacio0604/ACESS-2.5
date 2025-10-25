<?php
$data = json_decode(file_get_contents("php://input"), true);
$newPassword = $data['newPassword'] ?? '';
$adminId = $data['adminId'] ?? null;

if (empty($newPassword) || empty($adminId)) {
    echo json_encode(["status" => "error", "message" => "Password and Admin ID are required."]);
    exit;
}

$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

$sql = "UPDATE users SET password = ? WHERE id = ? AND role = 'admin'"; 
$stmt = $conn->prepare($sql);  
$stmt->bind_param('si', $hashedPassword, $adminId);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Password reset successful"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to reset password"]);
}

$stmt->close();
$conn->close();  
?>