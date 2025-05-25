<?php
class UserController {
    private $userRepository;
    private $request;

    public function __construct(DataRepositoryInterface $userRepository, RequestInterface $request) {
        $this->userRepository = $userRepository;
        $this->request = $request;
    }

    public function getAllUsers() {
        $users = $this->userRepository->getAll();
        
        foreach ($users as &$user) {
            unset($user['password']);
        }
        
        return new Response(200, json_encode([
            'status' => 'success',
            'data' => $users
        ]));
    }

    public function getUserById($id) {
        if (!is_numeric($id)) {
            return new Response(400, json_encode([
                'status' => 'error',
                'message' => 'Invalid ID format'
            ]));
        }

        $user = $this->userRepository->getById($id);
        if (!$user) {
            return new Response(404, json_encode([
                'status' => 'error',
                'message' => 'User not found'
            ]));
        }
        
        unset($user['password']);
        
        return new Response(200, json_encode([
            'status' => 'success',
            'data' => $user
        ]));
    }

    public function createUser() {
        $data = $this->request->getBody();
        
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            return new Response(400, json_encode([
                'status' => 'error',
                'message' => 'Name, email, and password are required'
            ]));
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return new Response(400, json_encode([
                'status' => 'error', 
                'message' => 'Invalid email format'
            ]));
        }
        
        if ($this->userRepository->getByEmail($data['email'])) {
            return new Response(409, json_encode([
                'status' => 'error',
                'message' => 'Email already in use'
            ]));
        }

        try {
            $userId = $this->userRepository->create($data);
            $user = $this->userRepository->getById($userId);
            
            unset($user['password']);
            
            return new Response(201, json_encode([
                'status' => 'success',
                'message' => 'User created successfully',
                'data' => $user
            ]));
        } catch (Exception $e) {
            return new Response(500, json_encode([
                'status' => 'error',
                'message' => 'Failed to create user: ' . $e->getMessage()
            ]));
        }
    }

    public function updateUser($id) {
        if (!is_numeric($id)) {
            return new Response(400, json_encode([
                'status' => 'error',
                'message' => 'Invalid ID format'
            ]));
        }
        
        if (!$this->userRepository->getById($id)) {
            return new Response(404, json_encode([
                'status' => 'error',
                'message' => 'User not found'
            ]));
        }
        
        $data = $this->request->getBody();
        
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return new Response(400, json_encode([
                'status' => 'error',
                'message' => 'Invalid email format'
            ]));
        }
        
        if (!empty($data['email'])) {
            $existingUser = $this->userRepository->getByEmail($data['email']);
            if ($existingUser && $existingUser['id'] != $id) {
                return new Response(409, json_encode([
                    'status' => 'error',
                    'message' => 'Email already in use by another user'
                ]));
            }
        }

        try {
            $this->userRepository->update($id, $data);
            $updatedUser = $this->userRepository->getById($id);
            
            unset($updatedUser['password']);
            
            return new Response(200, json_encode([
                'status' => 'success',
                'message' => 'User updated successfully',
                'data' => $updatedUser
            ]));
        } catch (Exception $e) {
            return new Response(500, json_encode([
                'status' => 'error',
                'message' => 'Failed to update user: ' . $e->getMessage()
            ]));
        }
    }

    public function deleteUser($id) {
        if (!is_numeric($id)) {
            return new Response(400, json_encode([
                'status' => 'error',
                'message' => 'Invalid ID format'
            ]));
        }
        
        if (!$this->userRepository->getById($id)) {
            return new Response(404, json_encode([
                'status' => 'error',
                'message' => 'User not found'
            ]));
        }

        try {
            $this->userRepository->delete($id);
            return new Response(200, json_encode([
                'status' => 'success',
                'message' => 'User deleted successfully'
            ]));
        } catch (Exception $e) {
            return new Response(500, json_encode([
                'status' => 'error',
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ]));
        }
    }
}
