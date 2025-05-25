<?php
namespace App\Controllers;

use App\Core\Session;
use App\Core\Middleware;
use App\Models\Student;
use App\Models\Course;
use App\Models\User;

class ReportController {
    protected $session;
    protected $middleware;
    protected $student;
    protected $course;
    protected $user;
    
    public function __construct() {
        global $db;
        $this->session = new Session();
        $this->middleware = new Middleware();
        $this->student = new Student($db);
        $this->course = new Course($db);
        $this->user = new User($db);
        
        $this->middleware->requireAdmin();
    }
    
    public function index() {
        $studentCount = $this->student->getCount();
        $courseCount = $this->course->getCount();
        $userCount = $this->user->getCount();
        
        $recentStudents = $this->student->getRecent(5);
        $recentCourses = $this->course->getRecent(5);
        
        require __DIR__ . '/../views/reports/index.php';
    }
}
