<?php
session_start();
header('Content-Type: application/json');

require_once '../../Accounts/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to vote']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if voting is active
$check_voting = $conn->query("SELECT voting_active FROM voting_settings WHERE id = 1");
$voting_status = $check_voting->fetch_assoc();

if (!$voting_status || !$voting_status['voting_active']) {
    echo json_encode(['success' => false, 'message' => 'Voting is currently not active']);
    exit;
}

// Check if user already voted
$check_vote = $conn->prepare("SELECT id FROM user_votes WHERE user_id = ?");
$check_vote->bind_param("i", $user_id);
$check_vote->execute();
$already_voted = $check_vote->get_result();

if ($already_voted->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already voted']);
    exit;
}

// Get vote data
$input = json_decode(file_get_contents("php://input"), true);

if (!$input || empty($input)) {
    echo json_encode(['success' => false, 'message' => 'No votes received']);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Record that user has voted
    $record_vote = $conn->prepare("INSERT INTO user_votes (user_id) VALUES (?)");
    $record_vote->bind_param("i", $user_id);
    $record_vote->execute();
    
    // Process each vote
    $update_vote = $conn->prepare("UPDATE voting_candidates SET votes = votes + 1 WHERE id = ?");
    $record_detail = $conn->prepare("INSERT INTO vote_details (user_id, candidate_id, position) VALUES (?, ?, ?)");
    
    foreach ($input as $position => $candidate_id) {
        // Increment candidate vote count
        $update_vote->bind_param("i", $candidate_id);
        $update_vote->execute();
        
        // Record vote detail
        $record_detail->bind_param("iis", $user_id, $candidate_id, $position);
        $record_detail->execute();
    }
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Your vote has been submitted successfully!'
    ]);
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Failed to submit vote: ' . $e->getMessage()
    ]);
}

$conn->close();
?>