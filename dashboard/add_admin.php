<?php
// Simple script to add admin user

// Connect to MySQL
$mysqli = new mysqli('localhost', 'root', '', 'dashboard_db');

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Create a password hash
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

// Clear out existing admin users
$mysqli->query("DELETE FROM users WHERE username='admin'");

// Insert new admin user
$stmt = $mysqli->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $hash);

$username = 'admin';
$email = 'admin@example.com';

$result = $stmt->execute();

if ($result) {
    echo "Admin user created successfully with password: $password";
} else {
    echo "Error creating admin user: " . $mysqli->error;
}

$mysqli->close();
