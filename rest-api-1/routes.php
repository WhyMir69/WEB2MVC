<?php
/**
 * Route definitions for the REST API
 */

$router->addRoute('GET', '/', function() {
    return new Response(200, json_encode([
        'message' => 'Welcome to the REST API', 
        'version' => '1.0'
    ]));
});

$router->addRoute('GET', '/login', function() {
    header('Content-Type: text/html');
    include __DIR__ . '/login_test.html';
    exit;
});

$router->addRoute('GET', '/token-test', function() {
    header('Content-Type: text/html');
    include __DIR__ . '/token_test.html';
    exit;
});

$router->addRoute('GET', '/fix-database', function() {
    header('Content-Type: text/html');
    include __DIR__ . '/fix_database.php';
    exit;
});

$router->addRoute('GET', '/test-auth', function() {
    header('Content-Type: text/html');
    include __DIR__ . '/test_authentication.php';
    exit;
});

$router->addRoute('POST', '/login', ['AuthController', 'login']);
$router->addRoute('POST', '/register', ['AuthController', 'register']);
$router->addRoute('GET', '/logout', ['AuthController', 'logout'], ['Middleware', 'authenticate']);
$router->addRoute('GET', '/users', ['UserController', 'getAllUsers'], ['Middleware', 'authenticate']);
$router->addRoute('GET', '/users/{id}', ['UserController', 'getUserById'], ['Middleware', 'authenticate']);
$router->addRoute('POST', '/users', ['UserController', 'create'], ['Middleware', 'authenticate']);
$router->addRoute('PUT', '/users/{id}', ['UserController', 'update'], ['Middleware', 'authenticate']);
$router->addRoute('DELETE', '/users/{id}', ['UserController', 'delete'], ['Middleware', 'authenticate']);




$router->addRoute('GET', '/', function() {
    return new Response(200, json_encode([
        'message' => 'Welcome to the REST API',
        'version' => '1.0'
    ]));
});

// Add these routes to your routes.php file

// Route to serve the registration page HTML
$router->addRoute('GET', '/register', function() {
    header('Content-Type: text/html');
    include __DIR__ . '/register.html';
    exit;
});

// Route to handle the registration form submission
$router->addRoute('POST', '/register', ['AuthController', 'register']);
