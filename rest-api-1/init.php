<?php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/classes/' . $class . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    } else {
        throw new Exception("Class file not found. Looked in: " . $file);
    }
});

require_once __DIR__ . '/classes/JwtHandler.php';
require_once __DIR__ . '/classes/Middleware.php';
?>