<?php
namespace App\Core;

class Middleware {
    protected $session;
    
    public function __construct() {
        $this->session = new Session();
    }
    
    public function requireAuth() {
        if (!$this->session->has('user')) {
            header('Location: /WEB2Finals/dashboard/login');
            exit;
        }
    }
    
    public function requireRole($roles) {
        $this->requireAuth();
        
        if (!$this->session->hasRole($roles)) {
            header('Location: /WEB2Finals/dashboard/errors/403');
            exit;
        }
    }
    
    public function requireAdmin() {
        $this->requireRole('admin');
    }
    
    public function guestOnly() {
        if ($this->session->has('user')) {
            header('Location: /WEB2Finals/dashboard/dashboard');
            exit;
        }
    }
}
