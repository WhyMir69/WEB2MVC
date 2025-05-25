<?php
class RouteMatcher {
    public function match($routes, $requestMethod, $requestPath) {
        // Normalize path - ensure it starts with / and doesn't end with / unless it's the root path
        $requestPath = '/' . trim($requestPath, '/');
        if ($requestPath !== '/') {
            $requestPath = rtrim($requestPath, '/');
        }
        
        error_log("RouteMatcher checking path: $requestPath");
        
        foreach ($routes as $route) {
            // Normalize route path in the same way
            $routePath = '/' . trim($route['path'], '/');
            if ($routePath !== '/') {
                $routePath = rtrim($routePath, '/');
            }
            
            $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $routePath);
            $pattern = "@^" . $pattern . "$@D";
            
            error_log("Matching against pattern: $pattern");

            if ($route['method'] === $requestMethod && preg_match($pattern, $requestPath, $matches)) {
                $result = [
                    'handler' => $route['handler'],
                    'params' => array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY)
                ];
                
                if (isset($route['middleware'])) {
                    $result['middleware'] = $route['middleware'];
                }
                
                return $result;
            }
        }
        return null;
    }
}
