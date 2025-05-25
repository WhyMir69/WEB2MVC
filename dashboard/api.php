<?php
// Simple API test file for Postman
header('Content-Type: application/json');
require_once 'app/libraries/ApiResponse.php';

// Force API mode
if (isset($_GET['endpoint'])) {
    $endpoint = $_GET['endpoint'];
    
    // Database connection
    require_once 'config/database.php';
    require_once 'app/core/Database.php';
    
    $config = require 'config/database.php';
    $db = new \App\Core\Database($config);
    
    if ($endpoint === 'students') {
        // Get students
        require_once 'app/models/Student.php';
        $studentModel = new \App\Models\Student($db);
        $students = $studentModel->getAll();
        
        echo json_encode([
            'status' => 'success',
            'data' => $students
        ]);
    } else if ($endpoint === 'courses') {
        // Get courses
        require_once 'app/models/Course.php';
        $courseModel = new \App\Models\Course($db);
        $courses = $courseModel->getAll();
        
        echo json_encode([
            'status' => 'success',
            'data' => $courses
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Unknown endpoint'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'success',
        'message' => 'API is working',
        'endpoints' => [
            'students' => '/api.php?endpoint=students',
            'courses' => '/api.php?endpoint=courses'
        ]
    ]);
}
