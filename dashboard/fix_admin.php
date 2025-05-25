<?php
try {
    // Connect to the database
    $host = 'localhost';
    $dbname = 'dashboard_db';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Generate a new hash for 'admin123'
    $adminPassword = 'admin123';
    $adminHash = password_hash($adminPassword, PASSWORD_DEFAULT);
    
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        // Update existing admin
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
        $stmt->execute([$adminHash]);
        echo "Admin user updated with new password hash.\n";
    } else {
        // Create new admin
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute(['admin', 'admin@example.com', $adminHash]);
        echo "New admin user created.\n";
    }
    
    // Verify the hash works
    $verify = password_verify($adminPassword, $adminHash);
    echo "Password verification: " . ($verify ? "Success" : "Failed") . "\n";
    echo "Admin hash: $adminHash\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
