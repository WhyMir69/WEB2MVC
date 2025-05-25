<?php

spl_autoload_register(function ($class) {
    error_log("Attempting to load class: $class");

    if (class_exists($class, false)) {
        return;
    }

    // Root directory for namespaced classes (e.g., Core, Controllers, etc.)
    $baseDir = __DIR__ . '/app';
    $file = $baseDir . '/' . str_replace('\\', '/', $class) . '.php';

    error_log("Looking for file: $file");

    if (file_exists($file)) {
        require_once $file;
        error_log("File found: $file");
    } else {
        error_log("File not found: $file");
        throw new Exception("Class not found: $class");
    }
});



