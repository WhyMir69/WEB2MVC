<?php
// Generate a password hash for 'admin123'
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password: $password\n";
echo "Hash: $hash\n";

// Verify the hash
$verify = password_verify($password, $hash);
echo "Verification result: " . ($verify ? "Success" : "Failed") . "\n";

// Verify the existing hash from schema.sql
$existingHash = '$2y$10$8uRbD/KKmET0L0bhyHFHdOJFyS8Oq71FGQtpBEyWzKgJHEWpYlK9C';
$verifyExisting = password_verify($password, $existingHash);
echo "Existing hash verification: " . ($verifyExisting ? "Success" : "Failed") . "\n";
