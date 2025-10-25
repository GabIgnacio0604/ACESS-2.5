<?php
header('Content-Type: application/json');
session_start();

// Include database connection
require_once '../../Accounts/db_connection.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Decode JSON input from fetch()
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (
    isset($input['club_name']) &&
    isset($input['adviser']) &&
    isset($input['group_chat'])
) {
    $club_name = trim($input['club_name']);
    $adviser = trim($input['adviser']);
    $group_chat = trim($input['group_chat']);

    // Check if club already exists
    $checkStmt = $conn->prepare("SELECT id FROM clubs WHERE club_name = ?");
    $checkStmt->bind_param("s", $club_name);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'A club with this name already exists']);
        $checkStmt->close();
        $conn->close();
        exit;
    }
    $checkStmt->close();

    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert the club
        $stmt = $conn->prepare("INSERT INTO clubs (club_name, adviser, group_chat) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $club_name, $adviser, $group_chat);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create club: ' . $stmt->error);
        }
        
        $clubId = $conn->insert_id;
        $stmt->close();
        
        // Get the adviser's user ID
        $adviserStmt = $conn->prepare("SELECT id FROM users WHERE fullname = ? AND role = 'teacher' LIMIT 1");
        $adviserStmt->bind_param("s", $adviser);
        $adviserStmt->execute();
        $adviserResult = $adviserStmt->get_result();
        
        if ($adviserResult->num_rows > 0) {
            $adviserData = $adviserResult->fetch_assoc();
            $adviserId = $adviserData['id'];
            
            // Add adviser as a club member with advisor role
            $memberStmt = $conn->prepare("INSERT INTO club_members (club_id, user_id, role, approved_at) VALUES (?, ?, 'advisor', NOW())");
            $memberStmt->bind_param("ii", $clubId, $adviserId);
            $memberStmt->execute();
            $memberStmt->close();
            
            // Create initial welcome message in club group chat
            $adminId = $_SESSION['user_id'];
            $welcomeMessage = "Welcome to $club_name! This is the official group chat for club members and advisers.";
            
            $msgStmt = $conn->prepare("INSERT INTO messages (sender_id, club_id, message, message_type) VALUES (?, ?, ?, 'club')");
            $msgStmt->bind_param("iis", $adminId, $clubId, $welcomeMessage);
            $msgStmt->execute();
            $msgStmt->close();
        }
        $adviserStmt->close();
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Club created successfully with group chat!',
            'club_id' => $clubId
        ]);
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
}

$conn->close();
?>