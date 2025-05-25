<?php
// Check database structure
require_once 'classes/Database.php';

try {
    // Connect to database
    $db = new Database('localhost', 'api_user', 'secure_password_123', 'rest_api');
    
    // Get table structure
    $tables = $db->query("SHOW TABLES");
    echo "Database tables:\n";
    foreach ($tables as $table) {
        $tableName = $table[array_key_first($table)];
        echo "- $tableName\n";
        
        // Get columns
        $columns = $db->query("DESCRIBE `$tableName`");
        echo "  Columns:\n";
        foreach ($columns as $column) {
            echo "  - {$column['Field']} ({$column['Type']})\n";
        }
        
        // Sample data
        $sample = $db->query("SELECT * FROM `$tableName` LIMIT 1");
        if (!empty($sample)) {
            echo "  Sample data:\n";
            foreach ($sample[0] as $key => $value) {
                echo "  - $key: " . (is_null($value) ? 'NULL' : $value) . "\n";
            }
        }
        
        echo "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
