<?php
// Include the database class
require_once __DIR__ . '/classes/Database.php';

// Set content type to text/plain for better readability in browser
header('Content-Type: text/plain');

try {
    // Create database connection with default credentials
    // Update these if your MySQL credentials are different
    $db = new Database();
    
    echo "Connected to database successfully\n";
    
    // Generate a fresh password hash for 'password123'
    $password = 'password123';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    echo "Generated new hash for 'password123': $hash\n";
    
    // Update the user's password
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hash, $email);
    $email = 'john.doe@example.com';
    
    if ($stmt->execute()) {
        echo "Password updated successfully for john.doe@example.com\n";
        
        // Verify the update worked
        $checkStmt = $db->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            echo "\nUser found in database:\n";
            echo "ID: " . $user['id'] . "\n";
            echo "Name: " . $user['name'] . "\n";
            echo "Email: " . $user['email'] . "\n";
            echo "Password Hash: " . $user['password'] . "\n";
            
            // Verify the password works with the new hash
            $verified = password_verify($password, $user['password']);
            echo "\nPassword verification test: " . ($verified ? "SUCCESS" : "FAILED") . "\n";
        } else {
            echo "\nWARNING: User 'john.doe@example.com' not found in database.\n";
        }
    } else {
        echo "Error updating password: " . $stmt->error . "\n";
    }
    
    echo "\nYou can now try logging in with:\n";
    echo "Email: john.doe@example.com\n";
    echo "Password: password123\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    
    // If there's an issue with the connection, let's check if the database exists
    try {
        $tempDb = new mysqli('localhost', 'root', '');
        if (!$tempDb->select_db('rest_api')) {
            echo "\nThe database 'rest_api' does not exist. Let's create it:\n";
            
            if ($tempDb->query("CREATE DATABASE IF NOT EXISTS rest_api")) {
                echo "Database 'rest_api' created successfully.\n";
                
                // Now create the users table
                $tempDb->select_db('rest_api');
                $userTableSql = "CREATE TABLE IF NOT EXISTS `users` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `name` varchar(100) NOT NULL,
                    `email` varchar(100) NOT NULL UNIQUE,
                    `password` varchar(255) NOT NULL,
                    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
                
                if ($tempDb->query($userTableSql)) {
                    echo "Table 'users' created successfully.\n";
                    
                    // Insert test user
                    $name = 'John Doe';
                    $email = 'john.doe@example.com';
                    $password = password_hash('password123', PASSWORD_DEFAULT);
                    
                    $insertSql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
                    $stmt = $tempDb->prepare($insertSql);
                    $stmt->bind_param("sss", $name, $email, $password);
                    
                    if ($stmt->execute()) {
                        echo "Test user created successfully with email 'john.doe@example.com' and password 'password123'\n";
                    } else {
                        echo "Error creating test user: " . $stmt->error . "\n";
                    }
                } else {
                    echo "Error creating users table: " . $tempDb->error . "\n";
                }
            } else {
                echo "Error creating database: " . $tempDb->error . "\n";
            }
        }
    } catch (Exception $ex) {
        echo "Database setup error: " . $ex->getMessage() . "\n";
    }
}
?>