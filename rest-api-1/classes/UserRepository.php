<?php
class UserRepository implements DataRepositoryInterface {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getAll() {
        return $this->db->query("SELECT id, name, email FROM users");
    }

    public function getById($id) {
        $result = $this->db->query("SELECT * FROM users WHERE id = ?", [$id]);
        return $result ? $result[0] : null;
    }

    public function getByEmail($email) {
        $result = $this->db->query("SELECT * FROM users WHERE email = ?", [$email]);
        return $result ? $result[0] : null;
    }

    /**
     * Create a new user
     * 
     * @param array $data User data
     * @return int|bool The new user ID or false on failure
     */
    public function create($data) {
        try {
            // Log the data for debugging
            error_log('Creating user with data: ' . json_encode($data));
            
            // Use the query method instead of direct prepare/bind_param
            $result = $this->db->query(
                "INSERT INTO users (name, email, password) VALUES (?, ?, ?)",
                [$data['name'], $data['email'], $data['password']]
            );
            
            if ($result !== false) {
                $id = $this->db->getLastInsertId();
                error_log("User created successfully with ID: $id");
                return $id;
            }
            
            error_log('Failed to execute statement: ' . $this->db->error);
            return false;
        } catch (\Exception $e) {
            error_log("Exception in create method: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        if (isset($data['name'])) {
            $fields[] = "name = ?";
            $values[] = $data['name'];
        }
        
        if (isset($data['email'])) {
            $fields[] = "email = ?";
            $values[] = $data['email'];
        }
        
        if (isset($data['password'])) {
            $fields[] = "password = ?";
            $values[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        
        $this->db->query(
            "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?", 
            $values
        );
        
        return true;
    }

    public function delete($id) {
        $this->db->query("DELETE FROM users WHERE id = ?", [$id]);
        return true;
    }
}
