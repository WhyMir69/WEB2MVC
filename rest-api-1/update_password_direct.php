<?php
// This script directly sets a known working password hash for the test user

// Connect to database
$host = 'localhost';
$db = 'rest_api';
$user = 'api_user';
$password = 'secure_password_123';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Use a direct update with a known working hash for 'password123'
    // This hash was generated with password_hash('password123', PASSWORD_DEFAULT)
    $hash = '$2y$10$OtKzxqNdZyVrW/WaCSVd4Os6aS3STXQMvBSAK6TcJjFKmScBFdCci';
    
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $result = $stmt->execute([$hash, 'john.doe@example.com']);
    
    if ($result) {
        echo "Password updated successfully for john.doe@example.com\n";
        
        // Verify the update worked
        $check = $pdo->prepare("SELECT password FROM users WHERE email = ?");
        $check->execute(['john.doe@example.com']);
        $stored = $check->fetchColumn();
        
        echo "Stored hash: $stored\n";
        echo "Verification test: " . (password_verify('password123', $stored) ? "PASS" : "FAIL") . "\n";
    } else {
        echo "Failed to update password\n";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
