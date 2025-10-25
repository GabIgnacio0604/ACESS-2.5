<?php
require_once '../../Accounts/db_connection.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['name']) || !isset($data['position'])) {
        throw new Exception('Name and position are required');
    }

    $name = $data['name'];
    $position = $data['position'];

    // Insert into voting_candidates table
    $query = "INSERT INTO voting_candidates (candidate_name, position) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $name, $position);
    
    if ($stmt->execute()) {
        // Get all candidates after insertion
        $query = "SELECT * FROM voting_candidates ORDER BY position, candidate_name";
        $result = $conn->query($query);
        $candidates = [];
        
        while ($row = $result->fetch_assoc()) {
            $candidates[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Candidate added successfully',
            'candidates' => $candidates
        ]);
    } else {
        throw new Exception('Failed to add candidate');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>