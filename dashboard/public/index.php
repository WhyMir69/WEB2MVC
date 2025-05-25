<?php
require_once '../app/core/Database.php';
require_once '../app/core/Session.php';
require_once '../app/core/Router.php';

spl_autoload_register(function ($class) {
    $namespaces = [
        'App\\Controllers\\' => __DIR__ . '/../app/controllers/',
        'App\\Models\\' => __DIR__ . '/../app/models/',
        'App\\Core\\' => __DIR__ . '/../app/core/'
    ];
      foreach ($namespaces as $prefix => $base_dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $relative_class = substr($class, $len);
            
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
            
            if (file_exists($file)) {
                require_once $file;
                return true;
            }
        }
    }
      return false;
});

$session = new \App\Core\Session();

$config = require '../config/database.php';
$db = new \App\Core\Database($config);

$router = new \App\Core\Router();

$router->get('/', 'AuthController@loginForm');
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');

$router->get('/dashboard', 'DashboardController@index');

$router->get('/students', 'StudentController@index');
$router->get('/students/show', 'StudentController@show');
$router->get('/students/create', 'StudentController@createForm');
$router->post('/students/create', 'StudentController@create');
$router->get('/students/edit', 'StudentController@editForm');
$router->post('/students/update', 'StudentController@update');
$router->post('/students/delete', 'StudentController@delete');
$router->get('/students/delete', 'StudentController@delete'); // Add GET support for delete
$router->post('/students/enroll', 'StudentController@enroll'); // Add enrollment route
$router->post('/students/unenroll', 'StudentController@unenroll'); // Add unenrollment route

$router->get('/courses', 'CourseController@index');
$router->get('/courses/show', 'CourseController@show');
$router->get('/courses/create', 'CourseController@createForm');
$router->post('/courses/create', 'CourseController@create');
$router->get('/courses/edit', 'CourseController@editForm');
$router->post('/courses/update', 'CourseController@update');
$router->post('/courses/delete', 'CourseController@delete');
$router->get('/courses/delete', 'CourseController@delete'); // Add GET support for delete

// Reports routes (admin only)
$router->get('/reports', 'ReportController@index');

// Error routes
$router->get('/errors/403', function() use ($router) {
    $router->error(403);
});

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Remove the base URL from the URI path
$uri = str_replace('/WEB2Finals/dashboard', '', $uri);
// If the URI is empty, set it to '/'
if (empty($uri)) {
    $uri = '/';
}

try {
    $router->dispatch($uri, $_SERVER['REQUEST_METHOD']);
} catch (\Exception $e) {
    error_log($e->getMessage());
    
    http_response_code(500);
    require '../app/views/errors/500.php';
}
