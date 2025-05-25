<?php
namespace App\Core;

class Session {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    public function has($key) {
        return isset($_SESSION[$key]);
    }
    
    public function remove($key) {
        unset($_SESSION[$key]);
    }
    
    public function destroy() {
        session_destroy();
    }
    
    public function flash($key, $message) {
        $_SESSION['_flash'][$key] = $message;
    }
    
    public function getFlash($key, $default = null) {
        if (isset($_SESSION['_flash'][$key])) {
            $message = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);
            return $message;
        }
        
        return $default;
    }
    
    public function getUser() {
        return $this->get('user', null);
    }
    
    public function getUserRole() {
        $user = $this->getUser();
        return $user ? ($user['role'] ?? 'staff') : null;
    }
    
    public function hasRole($roles) {
        $userRole = $this->getUserRole();
        
        if (!$userRole) {
            return false;
        }
        
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        return in_array($userRole, $roles);
    }
    
    public function isAdmin() {
        return $this->hasRole('admin');
    }
}
