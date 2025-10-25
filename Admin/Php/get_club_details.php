<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../../Accounts/db_connection.php';

// Get club ID from request
$club_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$club_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Club ID is required'
    ]);
    exit;
}

try {
    // Get club details
    $club_query = "SELECT * FROM clubs WHERE id = ?";
    
    $stmt = $conn->prepare($club_query);
    $stmt->bind_param("i", $club_id);
    $stmt->execute();
    $club_result = $stmt->get_result();
    $club_details = $club_result->fetch_assoc();

    if (!$club_details) {
        echo json_encode([
            'success' => false,
            'message' => 'Club not found'
        ]);
        exit;
    }

    // Get club members with their roles
    $members_query = "SELECT 
                        u.id,
                        u.fullname,
                        u.email,
                        u.role,
                        cm.joined_at,
                        cm.approved_at,
                        CASE 
                            WHEN u.id = ? THEN 'President'
                            WHEN u.id = ? THEN 'Vice President'
                            ELSE 'Member'
                        END as club_role
                     FROM club_members cm
                     JOIN users u ON cm.user_id = u.id
                     WHERE cm.club_id = ?
                     ORDER BY 
                        CASE 
                            WHEN u.id = ? THEN 1
                            WHEN u.id = ? THEN 2
                            ELSE 3
                        END,
                        u.fullname";

    $stmt = $conn->prepare($members_query);
    $president_id = $club_details['president_id'] ?? null;
    $vice_president_id = $club_details['vice_president_id'] ?? null;
    $stmt->bind_param("iiiii", 
        $president_id, 
        $vice_president_id, 
        $club_id, 
        $president_id, 
        $vice_president_id
    );
    $stmt->execute();
    $members_result = $stmt->get_result();
    
    $members = [];
    while ($member = $members_result->fetch_assoc()) {
        $members[] = [
            'id' => $member['id'],
            'fullname' => $member['fullname'],
            'email' => $member['email'],
            'role' => $member['role'],
            'club_role' => $member['club_role'],
            'joined_at' => $member['joined_at'],
            'approved_at' => $member['approved_at']
        ];
    }

    // Prepare the response
    $response = [
        'success' => true,
        'club' => [
            'id' => $club_details['id'],
            'name' => $club_details['club_name']
        ],
        'members' => $members
    ];

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>