<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../Accounts/db_connection.php';

// Get the JSON payload
$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];

if (!$user_id) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit;
}

// Validate payload
if (!isset($data['club_id']) || empty($data['club_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Club ID is required'
    ]);
    exit;
}

if (!isset($data['action']) || empty($data['action']) || !in_array($data['action'], ['join', 'cancel'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Action is required and must be either "join" or "cancel"'
    ]);
    exit;
}

$club_id = $data['club_id'];
$action = $data['action'];

try {
    // Check if user is already a member
    $check_query = "SELECT id FROM club_members WHERE user_id = ? AND club_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $user_id, $club_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $isMember = $result->num_rows > 0;

    if ($action === 'join') {
        if ($isMember) {
            echo json_encode([
                'success' => false,
                'message' => 'You are already a member of this club'
            ]);
            exit;
        }

        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Insert new membership (pending approval)
            $insert_query = "INSERT INTO club_members (user_id, club_id) VALUES (?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("ii", $user_id, $club_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to join club");
            }
            
            // Note: User will get access to group chat only after approval (approved_at IS NOT NULL)
            // The teacher/adviser will approve them through the club details page
            
            $conn->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Successfully requested to join the club. Wait for adviser approval to access group chat.'
            ]);
            
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
        
    } else if ($action === 'cancel') {
        if (!$isMember) {
            echo json_encode([
                'success' => false,
                'message' => 'You are not a member of this club'
            ]);
            exit;
        }

        // Delete membership
        $delete_query = "DELETE FROM club_members WHERE user_id = ? AND club_id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("ii", $user_id, $club_id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Successfully left the club and group chat'
            ]);
        } else {
            throw new Exception("Failed to leave club");
        }
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();