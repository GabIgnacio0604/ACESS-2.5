<?php
$servername = "localhost";  
$username = "u465284186_ACESS_USER";      
$password = "Acess12345";  
$dbname = "u465284186_ACESS_DB";        

// Create connection with error reporting
$conn = new mysqli($servername, $username, $password, $dbname);

// Set charset to prevent encoding issues
$conn->set_charset("utf8mb4");

// Check connection
if ($conn->connect_error) {
    error_log("Database Connection Error: " . $conn->connect_error);
    die(json_encode([
        'success' => false, 
        'error' => 'Database connection failed. Please try again later.'
    ]));
}

?>