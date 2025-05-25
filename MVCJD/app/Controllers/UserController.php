<?php

namespace Controllers;

require_once __DIR__ . '/../../../JWT/classes/JWTUtils.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function authenticateUser($headers, $userRepository) {
    if (!isset($headers['Authorization'])) {
        return [false, 'Missing Authorization header'];
    }

    $authHeader = $headers['Authorization'];
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        return [false, 'Malformed token'];
    }

    $token = $matches[1];

    $decoded = JWTUtils::validateToken($token);
    if (!$decoded) {
        return [false, 'Invalid or expired token'];
    }

    $user = $userRepository->getById($decoded->userId);
    if (!$user || $user['token'] !== $token) {
        return [false, 'Token does not match stored session'];
    }

    return [true, $decoded];
}

class UserController {
    private $userRepository;
    private $request;

    public function __construct($userRepository, $request) {
        $this->userRepository = $userRepository;
        $this->request = $request;
    }

    public function registerUser() {
        // Handle form POST submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
            $name = $_POST['name'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $userId = $this->userRepository->createUser([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword
            ]);

            // Redirect to login page after successful registration
            header('Location: /login');
            exit;
        }

        // Handle JSON API registration (existing logic)
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['name'], $data['username'], $data['email'], $data['password'])) {
            return new Response(400, json_encode(['error' => 'Missing required fields']));
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $userId = $this->userRepository->createUser([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $hashedPassword
        ]);

        return new Response(201, json_encode([
            'message' => 'User registered successfully',
            'userId' => $userId
        ]));
    }

    public function loginUser() {
        session_start();
        if (isset($_SESSION['user_id'])) {
            header('Location: /user-dashboard');
            exit;
        }

        // Handle form POST submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Fetch user by email
            $user = $this->userRepository->getUserByEmail($email);
            if (!$user || !password_verify($password, $user['password'])) {
                // Optionally, set an error message in session and redirect back to login
                header('Location: /login');
                exit;
            }

            // Redirect to user dashboard after successful login
            $_SESSION['user_id'] = $user['uid'];
            header('Location: /user-dashboard');
            exit;
        }

        $this->showLoginForm();

        // Handle JSON API login (existing logic)
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['email'], $data['password'])) {
            return new Response(400, json_encode(['error' => 'Missing email or password']));
        }

        $user = $this->userRepository->getUserByEmail($data['email']);
        if (!$user || !password_verify($data['password'], $user['password'])) {
            return new Response(401, json_encode(['error' => 'Invalid credentials']));
        }

        // Generate JWT token
        $token = JWTUtils::generateToken([
            'userId' => $user['uid'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role']
        ]);

        // Store the token in the database
        $this->userRepository->updateToken($user['uid'], $token);

        // Return success response with token
        return new Response(200, json_encode([
            'message' => 'Login successful',
            'userId' => $user['uid'],
            'token' => $token
        ]));
    }

    public function showLoginForm() {
        include __DIR__ . '/../views/login.php';
    }

    public function showRegisterForm() {
        include __DIR__ . '/../views/register.php';
    }

    public function dashboard() {
        // 1. Try session-based authentication (browser)
        session_start();
        if (isset($_SESSION['user_id'])) {
            $user = $this->userRepository->getById($_SESSION['user_id']);
            if (!$user) {
                header('Location: /login');
                exit;
            }
            include __DIR__ . '/../views/user-dashboard.php';
            return;
        }

        // 2. Fallback to JWT-based authentication (API)
        $headers = getallheaders();
        list($authenticated, $result) = authenticateUser($headers, $this->userRepository);

        if (!$authenticated) {
            return new Response(401, json_encode(['error' => $result]));
        }

        $user = $this->userRepository->getById($result->userId);
        if (!$user) {
            return new Response(404, json_encode(['error' => 'User not found']));
        }

        // Show dashboard for API/JWT
        ob_start();
        include __DIR__ . '/../views/user-dashboard.php';
        $html = ob_get_clean();
        return new Response(200, $html, ['Content-Type' => 'text/html']);
    }

    public function getAllUsers() {
        $users = $this->userRepository->getAll();
        foreach ($users as &$user) {
            unset($user['password']);
        }
        return new Response(200, json_encode($users));
    }

    public function getUserById($id) {
        // Validate the ID
        if (!is_numeric($id)) {
            return new Response(400, json_encode(['error' => 'Invalid user ID']));
        }

        $user = $this->userRepository->getById($id);
        if (!$user) {
            return new Response(404, json_encode(['error' => 'User not found']));
        }

        unset($user['password']); // Remove password
        return new Response(200, json_encode($user));
    }

    public function createUser() {
        $data = $this->request->getBody();
        $this->userRepository->create($data);
        return new Response(201, json_encode(['message' => 'User created']));
    }

    public function updateUser($id) {
        if (!is_numeric($id)) {
            return new Response(400, json_encode(['error' => 'Invalid user ID']));
        }

        $data = $this->request->getBody();
        $user = $this->userRepository->getById($id);
        if (!$user) {
            return new Response(404, json_encode(['error' => 'User not found']));
        }

        $this->userRepository->update($id, $data);
        return new Response(200, json_encode(['message' => 'User updated successfully']));
    }

    public function deleteUser($id) {
        if (!is_numeric($id)) {
            return new Response(400, json_encode(['error' => 'Invalid user ID']));
        }

        $user = $this->userRepository->getById($id);
        if (!$user) {
            return new Response(404, json_encode(['error' => 'User not found']));
        }

        $this->userRepository->delete($id);
        return new Response(200, json_encode(['message' => 'User deleted successfully']));
    }

    public function getProfile() {
        $headers = getallheaders();  // native PHP function
        list($authenticated, $result) = authenticateUser($headers, $this->userRepository);

        if (!$authenticated) {
            return new Response(401, json_encode(['error' => $result]));
        }

        $user = $this->userRepository->getById($result->userId);

        return new Response(200, json_encode([
            'message' => 'Authenticated successfully',
            'user' => $user
        ]));
    }
}