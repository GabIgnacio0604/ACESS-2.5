<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../Accounts/db_connection.php';

try {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $candidateName = $_POST['candidate_name'] ?? '';
        $position = $_POST['position'] ?? '';
        
        if (empty($candidateName) || empty($position)) {
            throw new Exception('Candidate name and position are required');
        }
        
        // Handle photo upload with fixed paths
        $photoUrl = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            // Use __DIR__ for absolute path
            $uploadDir = __DIR__ . '/../../Images/Candidates/';
            
            // Create directory if it doesn't exist with proper permissions
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    throw new Exception('Failed to create upload directory');
                }
            }
            
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $_FILES['photo']['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mime_type, $allowed_types)) {
                throw new Exception('Invalid file type. Only images allowed.');
            }
            
            // Check file size (max 5MB)
            if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
                throw new Exception('File too large. Maximum 5MB allowed.');
            }
            
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $filename = 'candidate_' . time() . '_' . uniqid() . '.' . $ext;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
                // Store relative path for web access
                $photoUrl = '../Images/Candidates/' . $filename;
            } else {
                throw new Exception('Failed to upload photo');
            }
        }
        
        $query = "INSERT INTO voting_candidates (candidate_name, position, photo_url, votes) VALUES (?, ?, ?, 0)";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Database prepare failed: ' . $conn->error);
        }
        
        $stmt->bind_param("sss", $candidateName, $position, $photoUrl);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Candidate created successfully',
                'candidate_id' => $stmt->insert_id
            ]);
        } else {
            throw new Exception('Failed to create candidate: ' . $stmt->error);
        }
        
        $stmt->close();
        
    } elseif ($action === 'update') {
        $candidateId = $_POST['candidate_id'] ?? '';
        $candidateName = $_POST['candidate_name'] ?? '';
        $position = $_POST['position'] ?? '';
        
        if (empty($candidateId) || empty($candidateName) || empty($position)) {
            throw new Exception('Candidate ID, name and position are required');
        }
        
        // Handle photo upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            // Use __DIR__ for absolute path
            $uploadDir = __DIR__ . '/../../Images/Candidates/';
            
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    throw new Exception('Failed to create upload directory');
                }
            }
            
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $_FILES['photo']['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mime_type, $allowed_types)) {
                throw new Exception('Invalid file type. Only images allowed.');
            }
            
            // Check file size (max 5MB)
            if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
                throw new Exception('File too large. Maximum 5MB allowed.');
            }
            
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $filename = 'candidate_' . time() . '_' . uniqid() . '.' . $ext;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
                $photoUrl = '../Images/Candidates/' . $filename;
                
                // Get old photo to delete
                $oldPhotoQuery = "SELECT photo_url FROM voting_candidates WHERE id = ?";
                $oldStmt = $conn->prepare($oldPhotoQuery);
                $oldStmt->bind_param("i", $candidateId);
                $oldStmt->execute();
                $oldResult = $oldStmt->get_result();
                
                if ($oldRow = $oldResult->fetch_assoc()) {
                    $oldPhoto = $oldRow['photo_url'];
                    if ($oldPhoto && file_exists(__DIR__ . '/../../' . $oldPhoto)) {
                        unlink(__DIR__ . '/../../' . $oldPhoto);
                    }
                }
                $oldStmt->close();
                
                $query = "UPDATE voting_candidates SET candidate_name = ?, position = ?, photo_url = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                
                if (!$stmt) {
                    throw new Exception('Database prepare failed: ' . $conn->error);
                }
                
                $stmt->bind_param("sssi", $candidateName, $position, $photoUrl, $candidateId);
            } else {
                throw new Exception('Failed to upload photo');
            }
        } else {
            // Update without photo
            $query = "UPDATE voting_candidates SET candidate_name = ?, position = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            
            if (!$stmt) {
                throw new Exception('Database prepare failed: ' . $conn->error);
            }
            
            $stmt->bind_param("ssi", $candidateName, $position, $candidateId);
        }
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Candidate updated successfully'
            ]);
        } else {
            throw new Exception('Failed to update candidate: ' . $stmt->error);
        }
        
        $stmt->close();
        
    } else {
        throw new Exception('Invalid action');
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