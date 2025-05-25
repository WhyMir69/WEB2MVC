<?php
// This script diagnoses and fixes login issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Connect directly to MySQL without using the Database class
    $pdo = new PDO("mysql:host=localhost;dbname=rest_api", "api_user", "secure_password_123");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Diagnosis</h2>";
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    $tableExists = $stmt->fetchColumn();
    
    if (!$tableExists) {
        echo "<p>The 'users' table does not exist. Creating it now...</p>";
        
        // Create the users table
        $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `name` VARCHAR(100) NOT NULL,
          `email` VARCHAR(100) NOT NULL,
          `password` VARCHAR(255) NOT NULL,
          `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `unique_email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        echo "<p>Users table created successfully!</p>";
    } else {
        echo "<p>Users table exists.</p>";
        
        // Check users table structure
        $stmt = $pdo->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p>Users table structure:</p><ul>";
        foreach ($columns as $column) {
            echo "<li>{$column['Field']} ({$column['Type']})</li>";
        }
        echo "</ul>";
        
        // Check if password column exists
        $passwordColumnExists = false;
        foreach ($columns as $column) {
            if ($column['Field'] === 'password') {
                $passwordColumnExists = true;
                break;
            }
        }
        
        if (!$passwordColumnExists) {
            echo "<p>The 'password' column does not exist. Adding it now...</p>";
            $pdo->exec("ALTER TABLE users ADD COLUMN password VARCHAR(255) NOT NULL");
            echo "<p>Password column added successfully!</p>";
        }
    }
    
    // Check if test user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['john.doe@example.com']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo "<p>Test user does not exist. Creating it now...</p>";
        
        // Create a test user
        $name = 'John Doe';
        $email = 'john.doe@example.com';
        $password = password_hash('password123', PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $password]);
        
        echo "<p>Test user created successfully!</p>";
        
        // Get the created user
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute(['john.doe@example.com']);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Update user's password
    echo "<p>Updating password for user: {$user['email']}</p>";
    
    $newPassword = 'password123';
    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
    
    echo "<p>Generated new hash: $newHash</p>";
    
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $result = $stmt->execute([$newHash, 'john.doe@example.com']);
    
    if ($result) {
        echo "<p>Password updated successfully!</p>";
        
        // Verify the update worked
        $stmt = $pdo->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->execute(['john.doe@example.com']);
        $stored = $stmt->fetchColumn();
        
        echo "<p>Stored hash: $stored</p>";
        
        $verified = password_verify('password123', $stored);
        echo "<p>Verification test: " . ($verified ? "SUCCESS" : "FAILED") . "</p>";
        
        if ($verified) {
            echo "<h3>✅ Login should now work correctly!</h3>";
            echo "<p>Please try logging in with:</p>";
            echo "<ul>";
            echo "<li>Email: john.doe@example.com</li>";
            echo "<li>Password: password123</li>";
            echo "</ul>";
        } else {
            echo "<h3>⚠️ Verification still fails. There might be an issue with password_verify()</h3>";
        }
    } else {
        echo "<p>Failed to update password</p>";
    }
    
} catch (PDOException $e) {
    echo "<h3>❌ Database Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
