<?php
namespace App\Controllers;

use App\Models\Course;
use App\Core\Session;
use App\Core\Middleware;

class CourseController {
    protected $course;
    protected $session;
    protected $middleware;
    
    public function __construct() {
        global $db;
        $this->course = new Course($db);
        $this->session = new Session();
        $this->middleware = new Middleware();
        
        $this->middleware->requireAuth();
    }    public function index() {
        $search = $_GET['search'] ?? '';
        $credits = $_GET['credits'] ?? '';
        $courses = $this->course->search($search, $credits);
        
        require_once __DIR__ . '/../libraries/ApiResponse.php';
        
        if (\ApiResponse::isApiRequest()) {
            \ApiResponse::sendSuccess($courses);
            exit;
        }
        
        require __DIR__ . '/../views/courses/index.php';
    }
    public function show() {
        $id = $_GET['id'] ?? null;
        
        require_once __DIR__ . '/../libraries/ApiResponse.php';
        
        if (!$id) {
            if (\ApiResponse::isApiRequest()) {
                \ApiResponse::sendError('Course ID is required', 400);
                exit;
            }
            $this->session->flash('error', 'Course ID is required');
            header('Location: /WEB2Finals/dashboard/courses');
            exit;
        }
        
        $course = $this->course->find($id);
        
        if (!$course) {
            if (\ApiResponse::isApiRequest()) {
                \ApiResponse::sendError('Course not found', 404);
                exit;
            }
            $this->session->flash('error', 'Course not found');
            header('Location: /WEB2Finals/dashboard/courses');
            exit;
        }
        
        // Get students enrolled in this course
        $enrolledStudents = $this->course->getStudents($id);
        
        if (\ApiResponse::isApiRequest()) {
            \ApiResponse::sendSuccess($course);
            exit;
        }
        
        require __DIR__ . '/../views/courses/show.php';
    }
      public function createForm() {
        require __DIR__ . '/../views/courses/create.php';
    }
      public function create() {
        if (empty($_POST['course_code']) || empty($_POST['title']) || !isset($_POST['credits'])) {
            $this->session->flash('error', 'Course code, title, and credits are required');
            header('Location: /WEB2Finals/dashboard/courses/create');
            exit;
        }
        
        $this->course->create([
            'course_code' => $_POST['course_code'],
            'title' => $_POST['title'],
            'description' => $_POST['description'] ?? null,
            'credits' => (int) $_POST['credits']
        ]);
        
        $this->session->flash('success', 'Course created successfully');
        header('Location: /WEB2Finals/dashboard/courses');
        exit;
    }
    
    public function editForm() {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $this->session->flash('error', 'Course ID is required');
            header('Location: /WEB2Finals/dashboard/courses');
            exit;
        }
        
        $course = $this->course->find($id);
          if (!$course) {
            $this->session->flash('error', 'Course not found');            header('Location: /WEB2Finals/dashboard/courses');
            exit;
        }
        
        require __DIR__ . '/../views/courses/edit.php';
    }
      public function update() {
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            $this->session->flash('error', 'Course ID is required');
            header('Location: /WEB2Finals/dashboard/courses');
            exit;
        }
        
        if (empty($_POST['course_code']) || empty($_POST['title']) || !isset($_POST['credits'])) {
            $this->session->flash('error', 'Course code, title, and credits are required');
            header("Location: /WEB2Finals/dashboard/courses/edit?id={$id}");
            exit;
        }
        
        $this->course->update($id, [
            'course_code' => $_POST['course_code'],
            'title' => $_POST['title'],
            'description' => $_POST['description'] ?? null,
            'credits' => (int) $_POST['credits']
        ]);
        
        $this->session->flash('success', 'Course updated successfully');
        header('Location: /WEB2Finals/dashboard/courses');
        exit;
    }    public function delete() {
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        
        if (!$id) {
            $this->session->flash('error', 'Course ID is required');
            header('Location: /WEB2Finals/dashboard/courses');
            exit;
        }
        
        // Check if the course has any enrolled students
        if ($this->course->hasEnrollments($id)) {
            $this->session->flash('error', 'Cannot delete course because it has enrolled students. Unenroll all students first.');
            header('Location: /WEB2Finals/dashboard/courses/show?id=' . $id);
            exit;
        }
        
        $this->course->delete($id);
        
        $this->session->flash('success', 'Course deleted successfully');
        header('Location: /WEB2Finals/dashboard/courses');
        exit;
    }
}
