<?php
namespace App\Controllers;

use App\Models\User;
use App\Core\Session;
use App\Core\Middleware;

class AuthController {
    protected $user;
    protected $session;
    protected $middleware;
    
    public function __construct() {
        global $db;
        $this->user = new User($db);
        $this->session = new Session();
        $this->middleware = new Middleware();
    }    public function loginForm() {
        $this->middleware->guestOnly();
        require __DIR__ . '/../views/auth/login.php';
    }
    
    public function login() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        file_put_contents('login_debug.txt', 
            "Login attempt: $username\n" . 
            "POST data: " . print_r($_POST, true) . "\n" . 
            "Session: " . print_r($_SESSION, true) . "\n",
            FILE_APPEND
        );
        
        if (empty($username) || empty($password)) {
            $this->session->flash('error', 'Username and password are required');
            header('Location: /WEB2Finals/dashboard/login');
            exit;
        }
        
        $user = $this->user->findByUsername($username);
        file_put_contents('login_debug.txt', 
            "User found: " . print_r($user, true) . "\n",
            FILE_APPEND
        );
        
        if ($user) {
            $verifyResult = password_verify($password, $user['password']);
            file_put_contents('login_debug.txt', 
                "Password verification result: " . ($verifyResult ? "SUCCESS" : "FAILED") . "\n" .
                "Input password: $password\n" .
                "Stored hash: " . $user['password'] . "\n",
                FILE_APPEND
            );
        }
        
        if (!$user || !password_verify($password, $user['password'])) {
            $this->session->flash('error', 'Invalid credentials');
            header('Location: /WEB2Finals/dashboard/login');
            exit;
        }
          $this->session->set('user', [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'] ?? 'staff'
        ]);
        
        header('Location: /WEB2Finals/dashboard/dashboard');
        exit;
    }
    
    public function logout() {
        $this->session->destroy();
        header('Location: /WEB2Finals/dashboard/login');
        exit;
    }
    
    public function registerForm() {
        $this->middleware->guestOnly();
        require __DIR__ . '/../views/auth/register.php';
    }
    
    public function register() {
        file_put_contents('register_debug.txt', 
            "Register attempt\n" . 
            "POST data: " . print_r($_POST, true) . "\n",
            FILE_APPEND
        );
        
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($username) || empty($email) || empty($password)) {
            $this->session->flash('error', 'All fields are required');
            header('Location: /WEB2Finals/dashboard/register');
            exit;
        }
        
        if ($password !== $confirmPassword) {
            $this->session->flash('error', 'Passwords do not match');
            header('Location: /WEB2Finals/dashboard/register');
            exit;
        }
        
        if ($this->user->findByUsername($username)) {
            $this->session->flash('error', 'Username already exists');
            header('Location: /WEB2Finals/dashboard/register');
            exit;
        }
        
        if ($this->user->findByEmail($email)) {
            $this->session->flash('error', 'Email already exists');
            header('Location: /WEB2Finals/dashboard/register');
            exit;
        }
        $userId = $this->user->create([
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'user' 
        ]);
        
        file_put_contents('register_debug.txt', 
            "User ID: " . ($userId ?: 'failed') . "\n" .
            "Database error: " . (error_get_last() ? json_encode(error_get_last()) : 'None') . "\n\n",
            FILE_APPEND
        );
        
        if ($userId) {
            $this->session->flash('success', 'Registration successful! Please log in.');
            header('Location: /WEB2Finals/dashboard/login');
            exit;
        } else {
            $this->session->flash('error', 'Registration failed');
            header('Location: /WEB2Finals/dashboard/register');
            exit;
        }
    }
}
