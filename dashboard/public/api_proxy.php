<?php
// Simple API proxy to support the URL format: /students
// This file maps the clean URL format to our api_students.php handler

// Determine the endpoint based on the request URI
$uri = $_SERVER['REQUEST_URI'];
$basePath = '/WEB2Finals/dashboard/';

if (strpos($uri, $basePath) === 0) {
    $path = substr($uri, strlen($basePath));
    $parts = explode('/', $path);
    
    if (count($parts) > 0) {
        $endpoint = $parts[0];
        
        // Map common endpoints
        if ($endpoint === 'students') {            // Pass to students API handler
            $_GET['endpoint'] = 'students';
            
            // Check if we have an ID (students/123)
            if (isset($parts[1]) && is_numeric($parts[1])) {
                $_GET['id'] = $parts[1];
            }
            
            // Include the API handler
            include __DIR__ . '/../api_students.php';
            exit;
        }
        else if ($endpoint === 'courses') {            // Pass to courses API handler
            $_GET['endpoint'] = 'courses';
            
            // Check if we have an ID (courses/123)
            if (isset($parts[1]) && is_numeric($parts[1])) {
                $_GET['id'] = $parts[1];
            }
            
            // Include the API handler
            include __DIR__ . '/../api_students.php';
            exit;
        }
    }
}

// If we get here, continue with normal request processing
// This allows the file to be placed in the public directory without
// breaking normal web requests
