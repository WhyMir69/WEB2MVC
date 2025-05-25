<?php

namespace Middlewares;

require_once __DIR__ . '/../../../JWT/classes/JWTUtils.php';

class AuthMiddleware implements MiddlewareInterface
{
    public function handle($request, $next)
    {
        // Try to get the Authorization header from all possible sources
        $authHeader = null;
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (isset($headers['Authorization'])) {
                $authHeader = $headers['Authorization'];
            } elseif (isset($headers['authorization'])) { // Some servers use lowercase
                $authHeader = $headers['authorization'];
            }
        }

        // Ensure the header starts with "Bearer "
        if (!$authHeader || strpos($authHeader, 'Bearer ') !== 0) {
            return new \Controllers\Response(401, json_encode(['error' => 'Unauthorized: Missing or invalid Authorization header']));
        }

        // Extract the token from the header
        $token = substr($authHeader, 7);

        // Validate the token using JWTUtils
        $decoded = JWTUtils::validateToken($token);

        // If the token is invalid, return a 401 Unauthorized response
        if (!$decoded) {
            return new \Controllers\Response(401, json_encode(['error' => 'Unauthorized: Invalid or expired token']));
        }

        // Attach the decoded user information to the request for later use
        $request->user = $decoded;

        // Pass the request to the next middleware or controller
        return $next($request);
    }
}
