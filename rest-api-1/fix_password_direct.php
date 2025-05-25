<?php
// This script updates the password hash for the test user directly

// Load required classes
require_once 'classes/Database.php';

try {
    // Connect to database 
    $db = new Database('localhost', 'api_user', 'secure_password_123', 'rest_api');
    
    // Generate a new hash for password123
    $password = 'password123';
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    
    echo "Generated new hash: $newHash\n";
    
    // Update user password
    $result = $db->query(
        "UPDATE users SET password = ? WHERE email = ?", 
        [$newHash, 'john.doe@example.com']
    );
    
    if ($result) {
        echo "Password updated successfully for john.doe@example.com";
    } else {
        echo "Failed to update password";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
