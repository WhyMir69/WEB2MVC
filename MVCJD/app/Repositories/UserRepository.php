<?php

namespace Repositories;

class UserRepository {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createUser($data) {
        $stmt = $this->db->getConnection()->prepare("
            INSERT INTO users (name, username, email, password) 
            VALUES (:name, :username, :email, :password)
        ");
        $stmt->execute([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password']
        ]);
        return $this->db->getConnection()->lastInsertId();
    }

    public function getById($id) {
        $stmt = $this->db->getConnection()->prepare("SELECT * FROM users WHERE uid = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getUserByEmail($email) {
        $stmt = $this->db->getConnection()->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        // Build the SET part of the SQL dynamically
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            if ($key === 'password') {
                // Optionally hash the password if it's being updated
                $value = password_hash($value, PASSWORD_DEFAULT);
            }
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }
        $params['id'] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE uid = :id";
        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateToken($userId, $token) {
        $sql = "UPDATE users SET token = :token WHERE uid = :uid";
        $this->db->execute($sql, ['token' => $token, 'uid' => $userId]);
    }

    public function delete($id) {
        $stmt = $this->db->getConnection()->prepare("DELETE FROM users WHERE uid = :id");
        return $stmt->execute(['id' => $id]);
    }
}
