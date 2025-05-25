<?php
namespace App\Models;

class User {
    protected $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function find($id) {
        return $this->db->query("SELECT * FROM users WHERE id = ?", [$id])->find();
    }
    
    public function findByUsername($username) {
        return $this->db->query("SELECT * FROM users WHERE username = ?", [$username])->find();
    }
    
    public function findByEmail($email) {
        return $this->db->query("SELECT * FROM users WHERE email = ?", [$email])->find();
    }    public function create(array $data) {
        try {
            $hasRoleColumn = false;
            try {
                $columns = $this->db->query("SHOW COLUMNS FROM users LIKE 'role'")->get();
                $hasRoleColumn = !empty($columns);
            } catch (\PDOException $e) {
                error_log("Error checking for role column: " . $e->getMessage());
            }
            
            if ($hasRoleColumn) {
                $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
                $params = [
                    $data['username'], 
                    $data['email'], 
                    $data['password'],
                    $data['role'] ?? 'user'
                ];
            } else {
                $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
                $params = [
                    $data['username'], 
                    $data['email'], 
                    $data['password']
                ];
            }
            $this->db->query($sql, $params);
            
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Error creating user: " . $e->getMessage() . " SQL: " . $sql);
            return false;
        }
    }
    
    public function getCount() {
        $result = $this->db->query("SELECT COUNT(*) as count FROM users")->find();
        return $result ? $result['count'] : 0;
    }
    
    public function getAll() {
        return $this->db->query("SELECT * FROM users ORDER BY username")->get();
    }
}
