<?php

namespace Core;

class Database {
    private \PDO $connection;

    public function __construct($host, $user, $password, $dbname) {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ];
        $this->connection = new \PDO($dsn, $user, $password, $options);
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            throw new \Exception("Database query failed: " . $e->getMessage());
        }
    }

    public function fetchOne($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            throw new \Exception("Database query failed: " . $e->getMessage());
        }
    }

    public function getLastInsertId(): int {
        return (int) $this->connection->lastInsertId();
    }

    public function execute($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new \Exception("Database query failed: " . $e->getMessage());
        }
    }

    public function getConnection(): \PDO {
        return $this->connection;
    }    
}