<?php
session_start();
header('Content-Type: application/json');

require_once '../../Accounts/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Check if voting is active
    $voting_query = "SELECT voting_active FROM voting_settings WHERE id = 1";
    $voting_result = $conn->query($voting_query);
    $voting_active = false;
    
    if ($voting_result && $voting_result->num_rows > 0) {
        $row = $voting_result->fetch_assoc();
        $voting_active = (bool)$row['voting_active'];
    }
    
    // Check if user has already voted
    $check_vote = $conn->prepare("SELECT id FROM user_votes WHERE user_id = ?");
    $check_vote->bind_param("i", $user_id);
    $check_vote->execute();
    $already_voted = $check_vote->get_result()->num_rows > 0;
    
    echo json_encode([
        'success' => true,
        'voting_active' => $voting_active,
        'already_voted' => $already_voted
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>