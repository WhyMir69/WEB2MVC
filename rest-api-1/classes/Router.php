<?php
class Router {
    private $request;
    private $routeMatcher;
    private $routes = [];
    private $userRepository;

    public function __construct(RequestInterface $request, RouteMatcher $routeMatcher, UserRepository $userRepository) {
        $this->request = $request;
        $this->routeMatcher = $routeMatcher;
        $this->userRepository = $userRepository;
    }

    public function addRoute($method, $path, $handler, $middleware = null) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    // Add this method
    public function getRoutes() {
        return $this->routes;
    }

    public function dispatch() {
        // Make sure empty paths are treated as root
        $path = $this->request->getPath();
        if ($path === '') {
            $path = '/';
        }
        
        $match = $this->routeMatcher->match(
            $this->routes,
            $this->request->getMethod(),
            $path
        );
        
        if ($match) {
            // Apply middleware if it exists
            if (isset($match['middleware'])) {
                $middlewareClass = $match['middleware'][0];
                $middlewareMethod = $match['middleware'][1];
                
                $middleware = new $middlewareClass($this->request, $this->userRepository);
                $middlewareResult = call_user_func_array([$middleware, $middlewareMethod], []);
                
                // If middleware returns a Response object, return it (authentication failed)
                if ($middlewareResult instanceof Response) {
                    return $middlewareResult;
                }
            }
            
            // Execute the route handler
            $handler = $match['handler'];
            if (is_array($handler)) {
                $controller = null;
                
                // Create appropriate controller instance based on class name
                if ($handler[0] === 'AuthController') {
                    $controller = new AuthController($this->userRepository, $this->request);
                } elseif ($handler[0] === 'UserController') {
                    $controller = new UserController($this->userRepository, $this->request);
                } else {
                    $controller = new $handler[0]($this->userRepository, $this->request);
                }
                
                return call_user_func_array([$controller, $handler[1]], $match['params'] ?? []);
            }
            
            return call_user_func_array($handler, $match['params'] ?? []);
        }

        return new Response(404, json_encode(['error' => 'Not Found']));
    }

    public function route($path, $method) {
        // Handle the root path
        if ($path === '') {
            return new Response(200, json_encode([
                'message' => 'Welcome to the REST API',
                'version' => '1.0'
            ]));
        }
        
        // Handle login route
        if ($path === 'login' && $method === 'POST') {
            $userRepository = new UserRepository();
            $request = new Request();
            $authController = new AuthController($userRepository, $request);
            return $authController->login();
        }
        
        // Handle other routes
        // ...
        
        // Route not found
        return new Response(404, json_encode(['error' => 'Not Found']));
    }
}
