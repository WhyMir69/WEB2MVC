<?php
// Add role column to users table if it doesn't exist
require_once __DIR__ . '/config/database.php';

$config = require __DIR__ . '/config/database.php';
$dsn = "mysql:host={$config['host']};dbname={$config['name']}";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $config['username'], $config['password'], $options);
    
    // Check if role column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'");
    $roleExists = $stmt->rowCount() > 0;
    
    if (!$roleExists) {
        // Add the role column
        $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'user' AFTER email");
        echo "Role column added successfully.";
    } else {
        echo "Role column already exists.";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
