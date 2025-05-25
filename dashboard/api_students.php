<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once 'app/core/Database.php';
require_once 'config/database.php';

$config = require 'config/database.php';
$db = new \App\Core\Database($config);

// Parse request
$endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : null;
$method = $_SERVER['REQUEST_METHOD'];

// Get request data
$data = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    $data = $_POST;
}

try {
    // Handle different endpoints
    if ($endpoint === 'students' || $endpoint === '') {
        // Load student model
        require_once 'app/models/Student.php';
        $studentModel = new \App\Models\Student($db);
        
        if ($method === 'GET') {
            if ($id) {
                // Get single student
                $student = $studentModel->find($id);
                if ($student) {
                    echo json_encode(['status' => 'success', 'data' => $student]);
                } else {
                    http_response_code(404);
                    echo json_encode(['status' => 'error', 'message' => 'Student not found']);
                }
            } else {
                // Get all students
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                $students = $studentModel->search($search);
                echo json_encode(['status' => 'success', 'data' => $students]);
            }
        } else if ($method === 'POST') {
            // Create or update student
            if ($id) {
                // Update student
                $result = $studentModel->update($id, [
                    'first_name' => $data['first_name'] ?? '',
                    'last_name' => $data['last_name'] ?? '',
                    'email' => $data['email'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'address' => $data['address'] ?? null,
                    'date_of_birth' => $data['date_of_birth'] ?? null
                ]);
                
                if ($result) {
                    $student = $studentModel->find($id);
                    echo json_encode(['status' => 'success', 'message' => 'Student updated', 'data' => $student]);
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Failed to update student']);
                }
            } else {
                // Create student
                $result = $studentModel->create([
                    'first_name' => $data['first_name'] ?? '',
                    'last_name' => $data['last_name'] ?? '',
                    'email' => $data['email'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'address' => $data['address'] ?? null,
                    'date_of_birth' => $data['date_of_birth'] ?? null
                ]);
                
                if ($result) {
                    echo json_encode(['status' => 'success', 'message' => 'Student created']);
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Failed to create student']);
                }
            }
        } else if ($method === 'DELETE') {
            // Delete student
            if (!$id) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Student ID is required']);
                exit;
            }
            
            $result = $studentModel->delete($id);
            
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Student deleted']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete student']);
            }
        }    } else if ($endpoint === 'courses') {
        // Load course model
        require_once 'app/models/Course.php';
        $courseModel = new \App\Models\Course($db);
        
        if ($method === 'GET') {
            if ($id) {
                // Get single course
                $course = $courseModel->find($id);
                if ($course) {
                    echo json_encode(['status' => 'success', 'data' => $course]);
                } else {
                    http_response_code(404);
                    echo json_encode(['status' => 'error', 'message' => 'Course not found']);
                }
            } else {
                // Get all courses
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                $credits = isset($_GET['credits']) ? $_GET['credits'] : '';
                $courses = $courseModel->search($search, $credits);
                echo json_encode(['status' => 'success', 'data' => $courses]);
            }
        } else if ($method === 'POST') {
            // Create or update course
            if ($id) {
                // Update course
                $result = $courseModel->update($id, [
                    'course_code' => $data['course_code'] ?? '',
                    'title' => $data['title'] ?? '',
                    'description' => $data['description'] ?? null,
                    'credits' => $data['credits'] ?? null
                ]);
                
                if ($result) {
                    $course = $courseModel->find($id);
                    echo json_encode(['status' => 'success', 'message' => 'Course updated', 'data' => $course]);
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Failed to update course']);
                }
            } else {
                // Create course
                $result = $courseModel->create([
                    'course_code' => $data['course_code'] ?? '',
                    'title' => $data['title'] ?? '',
                    'description' => $data['description'] ?? null,
                    'credits' => $data['credits'] ?? null
                ]);
                
                if ($result) {
                    echo json_encode(['status' => 'success', 'message' => 'Course created']);
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Failed to create course']);
                }
            }
        } else if ($method === 'DELETE') {
            // Delete course
            if (!$id) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Course ID is required']);
                exit;
            }
            
            $result = $courseModel->delete($id);
            
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Course deleted']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete course']);
            }
        }
    } else {
        // Unknown endpoint
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Unknown endpoint']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
