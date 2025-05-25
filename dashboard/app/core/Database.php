<?php
namespace App\Core;

class Database {
    protected $connection;
    protected $statement;
    
    public function __construct($config) {        $dsn = "mysql:host={$config['host']};dbname={$config['name']}";
        
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ];
          try {
            $this->connection = new \PDO(
                $dsn, $config['username'], $config['password'], $options
            );
        } catch (\PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    public function query($sql, $params = []) {
        $this->statement = $this->connection->prepare($sql);
        $this->statement->execute($params);
        return $this;
    }
    
    public function get() {
        return $this->statement->fetchAll();
    }
    
    public function find() {
        return $this->statement->fetch();
    }
      public function findOrFail() {
        $result = $this->find();
        
        if (!$result) {
            throw new \Exception('No results found.');
        }
        
        return $result;
    }
      public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
      public function prepare($sql) {
        return $this->connection->prepare($sql);
    }
    
    public function prepareAndExecute($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
