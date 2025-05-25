<?php
namespace App\Core;

class Router {
    protected $routes = [];
    
    public function add($method, $uri, $controller) {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller
        ];
    }
    
    public function get($uri, $controller) {
        $this->add('GET', $uri, $controller);
    }
    
    public function post($uri, $controller) {
        $this->add('POST', $uri, $controller);
    }
      public function dispatch($uri, $method) {
        if (strpos($uri, '?') !== false) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }
        
        $uri = rtrim($uri, '/');
        
        if ($uri === '') {
            $uri = '/';
        }
        
        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === strtoupper($method)) {
                return $this->callAction(
                    ...explode('@', $route['controller'])
                );
            }
        }
        
        $this->abort(404);
    }    protected function callAction($controller, $action) {
        $controllerWithNamespace = "App\\Controllers\\$controller";
        
        if (class_exists($controllerWithNamespace)) {
            $controller = new $controllerWithNamespace();
        } elseif (class_exists($controller)) {
            $controller = new $controller();
        } else {
            throw new \Exception("Controller {$controller} not found.");
        }
        
        if (!method_exists($controller, $action)) {
            throw new \Exception(
                get_class($controller) . " does not respond to the {$action} action."
            );
        }
        
        return $controller->$action();
    }    protected function abort($code) {
        http_response_code($code);
        
        if (file_exists(__DIR__ . "/../views/errors/{$code}.php")) {
            require __DIR__ . "/../views/errors/{$code}.php";
        } else {
            require __DIR__ . "/../views/errors/500.php";
        }
        
        die();
    }
    
    public function error($code) {
        $this->abort($code);
    }
}
