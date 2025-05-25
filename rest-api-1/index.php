<?php
// Turn off displaying errors in the output
ini_set('display_errors', 0);
// Log errors to file instead
ini_set('log_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/init.php';

require_once 'classes/Request.php';
require_once 'classes/Response.php';
require_once 'classes/Router.php';
require_once 'classes/RouteMatcher.php';
require_once 'classes/UserRepository.php';
require_once 'classes/JwtHandler.php';
require_once 'classes/AuthController.php';
require_once 'classes/UserController.php';
require_once 'classes/Middleware.php';

require_once 'bootstrap.php'; // Adjust if your bootstrap file has a different name

// Basic error handling
set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

$db = new Database('localhost', 'api_user', 'secure_password_123', 'rest_api');

$request = new Request();
$routeMatcher = new RouteMatcher();
$userRepository = new UserRepository($db);

$router = new Router($request, $routeMatcher, $userRepository);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit;
}

$requestUri = str_replace('/WEB2Finals/rest-api-1', '', $_SERVER['REQUEST_URI']);
if (($pos = strpos($requestUri, '?')) !== false) {
    $requestUri = substr($requestUri, 0, $pos);
}

if (empty($requestUri) || $requestUri === '') {
    $requestUri = '/';
}

error_log("Original Request URI: " . $_SERVER['REQUEST_URI']);
error_log("Processed Request URI: " . $requestUri);
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);

$_SERVER['REQUEST_URI'] = $requestUri;

$middleware = new Middleware($request, $userRepository);

$routeFile = __DIR__ . '/routes.php';
if (file_exists($routeFile)) {
    include $routeFile; 
} else {
    error_log('Routes file not found: ' . $routeFile);
}

try {
    $response = $router->dispatch();
    http_response_code($response->getStatusCode());
    header('Content-Type: application/json');
    echo $response->getBody();

    if ($response->getStatusCode() === 404) {
        error_log('Request path: ' . $request->getPath());
        error_log('Request method: ' . $request->getMethod());
        error_log('Available routes:');
        foreach ($router->getRoutes() as $index => $route) {
            error_log("Route $index: {$route['method']} {$route['path']}");
        }
    }
} catch (Exception $e) {
    // Handle any exceptions
    http_response_code(500);
    echo json_encode(['error' => 'Server Error: ' . $e->getMessage()]);
    error_log('Error in API: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
}
?>