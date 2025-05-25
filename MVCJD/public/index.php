<?php

require_once __DIR__ . '/../init.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// echo "INDEX LOADED";

try {
    // Initialize components
    $db = new \Core\Database('localhost', 'root', 'stalwart', 'usjr');
    // echo "Autoloader test passed: Database class loaded successfully!\n";

    $userRepository = new \Repositories\UserRepository($db);
    // echo "Autoloader test passed: UserRepository class loaded successfully!\n";

    $request = new \Core\Request();
    $controller = new \Controllers\UserController($userRepository, $request);
    // echo "Autoloader test passed: UserController class loaded successfully!\n";

    $method = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Normalize the URI to remove the base path
    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    $uri = preg_replace('#^' . preg_quote($basePath) . '#', '', $uri);
    if ($uri === '') $uri = '/';

    // echo "Requested URI: $uri\n";
    
    // Load routes
    $routes = include __DIR__ . '/../app/Routes/web.php';

    $matched = false;
    foreach ($routes as $route) {
        // Replace {id} with a regex group for matching
        $pattern = preg_replace('#\{id\}#', '(\d+)', $route['path']);
        $pattern = '#^' . $pattern . '$#';

        if (
            $method === $route['method'] &&
            preg_match($pattern, $uri, $matches)
        ) {
            $params = [];
            // If the route path contains {id}, add it as a parameter
            if (strpos($route['path'], '{id}') !== false) {
                $params[] = $matches[1];
            }

            // Use reflection to determine how many parameters the handler expects
            $reflection = new ReflectionFunction($route['handler']);
            $expectedParams = $reflection->getNumberOfParameters();

            // If the handler expects more parameters, add $request
            if (count($params) < $expectedParams) {
                $params[] = $request;
            }

            $response = call_user_func_array($route['handler'], $params);
            if ($response instanceof Response) {
                http_response_code($response->getStatusCode());
                header('Content-Type: application/json');
                echo $response->getBody();
            }
            $matched = true;
            break;
        }
    }

    if (!$matched) {
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
