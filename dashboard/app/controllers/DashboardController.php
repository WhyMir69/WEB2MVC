<?php
namespace App\Controllers;

use App\Core\Session;
use App\Core\Middleware;

class DashboardController {
    protected $session;
    protected $middleware;
    
    public function __construct() {
        $this->session = new Session();
        $this->middleware = new Middleware();
        
        $this->middleware->requireAuth();
    }    public function index() {
        $user = $this->session->get('user');
        require __DIR__ . '/../views/dashboard/index.php';
    }
}
