<?php
class Database {
    private $connection;
    public $error;

    /**
     * Constructor creates a new database connection
     */
    public function __construct($host = 'localhost', $username = 'root', $password = '', $database = 'rest_api') {
        $this->connection = new \mysqli($host, $username, $password, $database);
        
        if ($this->connection->connect_error) {
            $this->error = $this->connection->connect_error;
            error_log("Database connection failed: " . $this->error);
            throw new \Exception("Database connection failed: " . $this->error);
        }
    }

    /**
     * Execute a query with parameters and return results
     */
    public function query($sql, $params = []) {
        $stmt = $this->prepare($sql);
        
        if (!$stmt) {
            $this->error = $this->connection->error;
            error_log("Query preparation failed: " . $this->error);
            return false;
        }
        
        if (!empty($params)) {
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } elseif (is_string($param)) {
                    $types .= 's';
                } else {
                    $types .= 'b';
                }
            }
            
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            $this->error = $stmt->error;
            error_log("Query execution failed: " . $this->error);
            return false;
        }
        
        $result = $stmt->get_result();
        $data = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        
        $stmt->close();
        return $data;
    }
    
    /**
     * Prepare an SQL statement
     */
    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }
    
    /**
     * Get the ID of the last inserted row
     */
    public function getLastInsertId() {
        return $this->connection->insert_id;
    }
    
    /**
     * Close the database connection
     */
    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
    
    /**
     * Get the underlying mysqli connection
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Begin a transaction
     */
    public function beginTransaction() {
        $this->connection->begin_transaction();
    }
    
    /**
     * Commit a transaction
     */
    public function commit() {
        $this->connection->commit();
    }
    
    /**
     * Rollback a transaction
     */
    public function rollback() {
        $this->connection->rollback();
    }
}
