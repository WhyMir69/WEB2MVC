<?php
\
namespace Repositories;

declare(strict_types=1);

require_once 'iDBfuncs.php';

class DBORM implements iDBFuncs {

    private \PDO $pdo; // Explicitly declare the PDO property
    private string $sql = '';
    private int $whereInstanceCounter = 0;
    private array $valueBag = [];
    private string $table = '';

    public function __construct($dsn, $user, $pass) {
        try {
            $this->pdo = new \PDO($dsn, $user, $pass);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function select(?array $fieldList = null): object {
        $this->sql .= 'SELECT ';
        
        if ($fieldList === null) {
            $fieldList = '*';
            $this->sql .= $fieldList;
        } else {
            $contents = count($fieldList) - 1;
            $count = 0; 
            foreach ($fieldList as $field) {
                $this->sql .= $field;  
                if ($count < $contents) {
                    $this->sql .= ', ';
                }
                $count++;   
            }  
        }

        $this->sql .= ' ';
        return $this;
    }

    public function table($table): object {
        $this->table = $table;
        return $this;
    }

    public function insert(array $data): int {
        $fields = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $this->sql = "INSERT INTO {$this->table} ($fields) VALUES ($placeholders)";
        
        $stmt = $this->pdo->prepare($this->sql);
        $stmt->execute($data);
        return (int)$stmt->rowCount();
    }

    public function get(): array {
        $stmt = $this->pdo->prepare($this->sql);
        $stmt->execute($this->valueBag);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function where(array $conditions): object {
        if (empty($conditions)) {
            throw new \Exception('Conditions for WHERE clause cannot be empty.');
        }
    
        $clauses = [];
        $this->valueBag = [];
    
        foreach ($conditions as $field => $value) {
            $placeholder = ':' . $field;
            $clauses[] = "`$field` = $placeholder";
            $this->valueBag[$placeholder] = $value;
        }
    
        $this->sql .= 'WHERE ' . implode(' AND ', $clauses) . ' ';
        return $this;
    }

    public function whereOr(): object {
        return $this;
    }

    public function from($table): object {
        $this->sql .= 'FROM ' . $table . ' ';
        return $this;
    }

    public function update(array $values): int {
        if (empty($values)) {
            throw new \Exception('SET clause cannot be empty for the update operation.');
        }
    
        if (empty($this->valueBag)) {
            throw new \Exception('WHERE clause cannot be empty for the update operation.');
        }
    
        $setClause = [];
        foreach ($values as $field => $value) {
            $setClause[] = "`$field` = :$field";
        }
        $setClauseString = implode(', ', $setClause);
    
        $this->sql = "UPDATE {$this->table} SET $setClauseString " . $this->sql;
    
        $mergedValues = array_merge($values, $this->valueBag);
    
        $stmt = $this->pdo->prepare($this->sql);
        $stmt->execute($mergedValues);
    
        return (int)$stmt->rowCount();
    }    

    public function delete(): int {
        if (empty($this->valueBag)) {
            throw new \Exception('WHERE clause cannot be empty for the delete operation.');
        }

        $this->sql = "DELETE FROM {$this->table} " . $this->sql;

        $stmt = $this->pdo->prepare($this->sql);
        $stmt->execute($this->valueBag);
        return (int)$stmt->rowCount();
    }

    public function getAll(): array {
        $stmt = $this->pdo->prepare($this->sql);
        $stmt->execute($this->valueBag);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function showQuery(): string {
        return $this->sql;
    }

    public function showValueBag(): array {
        return $this->valueBag;
    }
}