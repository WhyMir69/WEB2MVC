<?php
try {
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $pdo->exec("CREATE DATABASE IF NOT EXISTS dashboard_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    $pdo->exec("USE dashboard_db");
    
    $sqlStatements = file_get_contents(__DIR__ . '/config/schema.sql');
    $pdo->exec($sqlStatements);
    
    echo "<h1>Database Setup Complete</h1>";
    echo "<p>The database has been successfully set up with all required tables and sample data.</p>";
    echo "<p><a href='/WEB2Finals/dashboard/login'>Go to Login</a></p>";
    
} catch (PDOException $e) {
    echo "<h1>Database Setup Error</h1>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your MySQL connection and try again.</p>";
}
