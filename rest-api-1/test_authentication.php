<?php
// Test login and JWT authentication
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once 'classes/Database.php';
require_once 'classes/JwtHandler.php';
require_once 'classes/UserRepository.php';

try {
    // Connect to database
    $db = new Database('localhost', 'api_user', 'secure_password_123', 'rest_api');
    $userRepository = new UserRepository($db);
    $jwtHandler = new JwtHandler();
    
    echo "<h2>JWT Authentication Test</h2>";
    
    // Step 1: Get user
    $email = 'john.doe@example.com';
    $password = 'password123';
    
    $user = $userRepository->getByEmail($email);
    
    if (!$user) {
        die("<p>Error: Test user not found</p>");
    }
    
    echo "<p>User found: " . htmlspecialchars(json_encode($user)) . "</p>";
    
    // Step 2: Verify password
    $passwordVerified = password_verify($password, $user['password']);
    
    echo "<p>Password verification: " . ($passwordVerified ? "SUCCESS" : "FAILED") . "</p>";
    echo "<p>Password from DB: " . htmlspecialchars($user['password']) . "</p>";
    
    // Create a new hash for testing
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    echo "<p>New hash for same password: " . htmlspecialchars($newHash) . "</p>";
    echo "<p>Verification with new hash: " . (password_verify($password, $newHash) ? "SUCCESS" : "FAILED") . "</p>";
    
    // Step 3: Generate token
    $token = $jwtHandler->generateToken($user['id']);
    
    echo "<p>Generated token: " . htmlspecialchars($token) . "</p>";
    
    // Step 4: Decode and verify token
    try {
        $decoded = $jwtHandler->decode($token);
        echo "<p>Token decoded successfully: " . htmlspecialchars(json_encode($decoded)) . "</p>";
        
        // Extract user ID and fetch user
        $userId = $decoded->data->userId;
        $userFromToken = $userRepository->getById($userId);
        
        if ($userFromToken) {
            echo "<p>User retrieved from token: " . htmlspecialchars(json_encode($userFromToken)) . "</p>";
            echo "<h3>✅ JWT Authentication working correctly!</h3>";
        } else {
            echo "<p>❌ Failed to retrieve user from token ID</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Token validation error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    // Test using the token in an API call
    echo "<h3>API Test with Token</h3>";
    echo "<p>To use this token for protected API endpoints, add the following header to your requests:</p>";
    echo "<pre>Authorization: Bearer " . htmlspecialchars($token) . "</pre>";
      echo "<p>You can test this in your login_test.html page or by using this link:</p>";
    echo "<a href='/WEB2Finals/rest-api-1/token-test?token=".urlencode($token)."' target='_blank'>Open Token Test Page</a>";
    
} catch (Exception $e) {
    echo "<h3>❌ Error:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
