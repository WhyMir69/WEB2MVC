<?php
// Display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Try to connect to MySQL with the API user credentials
try {
    $db = new mysqli('localhost', 'api_user', 'secure_password_123', 'rest_api');
    
    if ($db->connect_error) {
        die('Database connection failed: ' . $db->connect_error);
    }
    
    echo "Database connection successful! <br>";
    
    // Check if users table exists and has data
    $result = $db->query("SELECT COUNT(*) as count FROM users");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "Found {$row['count']} users in the database.";
    } else {
        echo "Error querying users table: " . $db->error;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
