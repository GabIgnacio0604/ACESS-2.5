<?php
header('Content-Type: application/json');
require_once '../../Accounts/db_connection.php';

try {
    // Get the candidate with highest votes for each position
    $query = "SELECT vc1.id, vc1.candidate_name, vc1.position, vc1.photo_url, vc1.votes
              FROM voting_candidates vc1
              INNER JOIN (
                  SELECT position, MAX(votes) as max_votes
                  FROM voting_candidates
                  GROUP BY position
              ) vc2 ON vc1.position = vc2.position AND vc1.votes = vc2.max_votes
              ORDER BY 
                CASE vc1.position 
                    WHEN 'President' THEN 1
                    WHEN 'Vice President' THEN 2
                    WHEN 'Secretary' THEN 3
                    WHEN 'Treasurer' THEN 4
                    WHEN 'Auditor' THEN 5
                    WHEN 'P.I.O' THEN 6
                    WHEN 'P.O' THEN 7
                    WHEN 'Grade 12' THEN 8
                    WHEN 'Grade 11' THEN 9
                    WHEN 'Grade 10' THEN 10
                    WHEN 'Grade 9' THEN 11
                    WHEN 'Grade 8' THEN 12
                    WHEN 'Grade 7' THEN 13
                    ELSE 14
                END";
    
    $result = $conn->query($query);
    
    if ($result) {
        $elected = [];
        $executive = [];
        $grades = [];
        
        while ($row = $result->fetch_assoc()) {
            $candidate = [
                'id' => $row['id'],
                'name' => $row['candidate_name'],
                'position' => $row['position'],
                'photo_url' => $row['photo_url'] ?? null,
                'votes' => $row['votes']
            ];
            
            // Separate executive positions and grade representatives
            if (in_array($row['position'], ['President', 'Vice President', 'Secretary', 'Treasurer', 'Auditor', 'P.I.O', 'P.O'])) {
                $executive[] = $candidate;
            } else {
                $grades[] = $candidate;
            }
            
            $elected[$row['position']] = $candidate;
        }
        
        echo json_encode([
            'success' => true,
            'elected' => $elected,
            'executive' => $executive,
            'grades' => $grades
        ]);
    } else {
        throw new Exception('Failed to fetch elected candidates');
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