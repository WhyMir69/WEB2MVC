<?php
namespace App\Controllers;

use App\Models\Student;
use App\Models\Course;
use App\Core\Session;
use App\Core\Middleware;

class StudentController {
    protected $student;
    protected $course;
    protected $session;
    protected $middleware;
    
    public function __construct() {
        global $db;
        $this->student = new Student($db);
        $this->course = new Course($db);
        $this->session = new Session();
        $this->middleware = new Middleware();
        
        $this->middleware->requireAuth();
    }public function index() {
        $search = $_GET['search'] ?? '';
        $students = $this->student->search($search);
        
        require_once __DIR__ . '/../libraries/ApiResponse.php';
        
        if (\ApiResponse::isApiRequest()) {
            \ApiResponse::sendSuccess($students);
            exit; 
        }
        
        require __DIR__ . '/../views/students/index.php';
    }
    
    public function show() {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $this->session->flash('error', 'Student ID is required');
            header('Location: /WEB2Finals/dashboard/students');
            exit;
        }
        
        $student = $this->student->find($id);
        if (!$student) {
            $this->session->flash('error', 'Student not found');
            header('Location: /WEB2Finals/dashboard/students');
            exit;
        }
        
        // Get the courses this student is enrolled in
        $enrolledCourses = $this->student->getCourses($id);
        
        // Get all available courses for enrollment
        $allCourses = $this->course->getAll();
        
        require __DIR__ . '/../views/students/show.php';
    }
      public function createForm() {
        require __DIR__ . '/../views/students/create.php';
    }
      public function create() {
        if (empty($_POST['first_name']) || empty($_POST['last_name'])) {
            $this->session->flash('error', 'First name and last name are required');
            header('Location: /WEB2Finals/dashboard/students/create');
            exit;
        }
        
        $this->student->create([
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'] ?? null,
            'phone' => $_POST['phone'] ?? null,
            'address' => $_POST['address'] ?? null,
            'date_of_birth' => $_POST['date_of_birth'] ?? null
        ]);
        
        $this->session->flash('success', 'Student created successfully');
        header('Location: /WEB2Finals/dashboard/students');
        exit;
    }
    
    public function editForm() {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $this->session->flash('error', 'Student ID is required');
            header('Location: /WEB2Finals/dashboard/students');
            exit;
        }
        
        $student = $this->student->find($id);        if (!$student) {
            $this->session->flash('error', 'Student not found');
            header('Location: /WEB2Finals/dashboard/students');
            exit;
        }
        
        require __DIR__ . '/../views/students/edit.php';
    }
      public function update() {
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            $this->session->flash('error', 'Student ID is required');
            header('Location: /WEB2Finals/dashboard/students');
            exit;
        }
        
        if (empty($_POST['first_name']) || empty($_POST['last_name'])) {
            $this->session->flash('error', 'First name and last name are required');
            header("Location: /WEB2Finals/dashboard/students/edit?id={$id}");
            exit;
        }
        
        $this->student->update($id, [
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'] ?? null,
            'phone' => $_POST['phone'] ?? null,
            'address' => $_POST['address'] ?? null,
            'date_of_birth' => $_POST['date_of_birth'] ?? null
        ]);
        
        $this->session->flash('success', 'Student updated successfully');
        header('Location: /WEB2Finals/dashboard/students');
        exit;
    }
      public function delete() {
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        
        if (!$id) {
            $this->session->flash('error', 'Student ID is required');
            header('Location: /WEB2Finals/dashboard/students');
            exit;
        }
        
        $this->student->delete($id);
        
        $this->session->flash('success', 'Student deleted successfully');
        header('Location: /WEB2Finals/dashboard/students');
        exit;
    }
    
    // Enroll a student in a course
    public function enroll() {
        $studentId = $_POST['student_id'] ?? null;
        $courseId = $_POST['course_id'] ?? null;
        
        if (!$studentId || !$courseId) {
            $this->session->flash('error', 'Student ID and Course ID are required');
            header("Location: /WEB2Finals/dashboard/students/show?id={$studentId}");
            exit;
        }
        
        $result = $this->student->enrollInCourse($studentId, $courseId);
        
        if ($result) {
            $this->session->flash('success', 'Student enrolled in course successfully');
        } else {
            $this->session->flash('info', 'Student is already enrolled in this course');
        }
        
        header("Location: /WEB2Finals/dashboard/students/show?id={$studentId}");
        exit;
    }
    
    // Unenroll a student from a course
    public function unenroll() {
        $studentId = $_POST['student_id'] ?? null;
        $courseId = $_POST['course_id'] ?? null;
        
        if (!$studentId || !$courseId) {
            $this->session->flash('error', 'Student ID and Course ID are required');
            header("Location: /WEB2Finals/dashboard/students/show?id={$studentId}");
            exit;
        }
        
        $this->student->unenrollFromCourse($studentId, $courseId);
        
        $this->session->flash('success', 'Student unenrolled from course successfully');
        header("Location: /WEB2Finals/dashboard/students/show?id={$studentId}");
        exit;
    }
}
