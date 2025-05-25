<?php
// JWT Test Script
// This script tests JWT authentication with the REST API

// API base URL - adjust for your environment
$base_url = 'http://localhost/WEB2Finals/rest-api-1';

// Test user credentials
$credentials = [
    'email' => 'john.doe@example.com',
    'password' => 'password123'
];

// Function to make API requests
function callApi($url, $method = 'GET', $data = null, $token = null) {
    $ch = curl_init($url);
    
    $headers = ['Content-Type: application/json'];
    if ($token) {
        $headers[] = "Authorization: Bearer $token";
    }
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($method !== 'GET') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $status,
        'response' => json_decode($response, true)
    ];
}

// Step 1: Test login
echo "Testing login...\n";
$login_result = callApi("$base_url/login", 'POST', $credentials);
echo "Status: " . $login_result['status'] . "\n";
echo "Response: " . json_encode($login_result['response'], JSON_PRETTY_PRINT) . "\n\n";

if ($login_result['status'] !== 200 || empty($login_result['response']['token'])) {
    echo "Login failed, cannot continue tests\n";
    exit(1);
}

$token = $login_result['response']['token'];
echo "Successfully obtained token: " . $token . "\n\n";

// Step 2: Test getting all users (authenticated endpoint)
echo "Testing GET /users (authenticated)...\n";
$users_result = callApi("$base_url/users", 'GET', null, $token);
echo "Status: " . $users_result['status'] . "\n";
echo "Response: " . json_encode($users_result['response'], JSON_PRETTY_PRINT) . "\n\n";

// Step 3: Test accessing protected endpoint without token
echo "Testing GET /users (unauthenticated)...\n";
$unauth_result = callApi("$base_url/users");
echo "Status: " . $unauth_result['status'] . "\n";
echo "Response: " . json_encode($unauth_result['response'], JSON_PRETTY_PRINT) . "\n\n";

// Step 4: Test getting a single user
echo "Testing GET /users/1 (authenticated)...\n";
$user_result = callApi("$base_url/users/1", 'GET', null, $token);
echo "Status: " . $user_result['status'] . "\n";
echo "Response: " . json_encode($user_result['response'], JSON_PRETTY_PRINT) . "\n\n";

echo "JWT Authentication test completed.\n";
