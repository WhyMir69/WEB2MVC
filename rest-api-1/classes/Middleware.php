<?php
/**
 * Middleware class for authentication and other pre-request processing
 */
class Middleware {
    private $request;
    private $userRepository;
    private $jwtHandler;

    /**
     * Constructor for Middleware
     * 
     * @param RequestInterface $request The request object
     * @param UserRepository $userRepository Repository for user data
     */
    public function __construct(RequestInterface $request, UserRepository $userRepository) {
        $this->request = $request;
        $this->userRepository = $userRepository;
        $this->jwtHandler = new JwtHandler();
    }

    /**
     * Authenticate the request using JWT
     * 
     * @return Response|true Response object on failure, true on success
     */
    public function authenticate() {
        error_log("Authentication middleware running");

        $authHeader = $this->request->getHeader('Authorization');
        error_log("Authorization header: " . ($authHeader ?: 'not found'));
        
        if (!$authHeader) {
            // Try alternative methods to get the authorization header
            $allHeaders = getallheaders();
            error_log("All headers: " . json_encode($allHeaders));
            
            if (isset($allHeaders['Authorization'])) {
                $authHeader = $allHeaders['Authorization'];
                error_log("Found Authorization in getallheaders(): " . $authHeader);
            } else {
                // Also check for Bearer token in other places
                $bearerToken = $this->request->getBearerToken();
                if ($bearerToken) {
                    $authHeader = "Bearer " . $bearerToken;
                    error_log("Found token using getBearerToken(): " . $authHeader);
                }
            }
            
            if (!$authHeader) {
                return new Response(401, json_encode([
                    'error' => 'Authorization header not found'
                ]));
            }
        }
        
        $token = null;
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
        }
        
        error_log("Extracted token: " . ($token ?: 'not found'));
        
        if (!$token) {
            return new Response(401, json_encode([
                'error' => 'Token not found in Authorization header'
            ]));
        }
        
        try {
            error_log("Attempting to decode token");
            $decoded = $this->jwtHandler->decode($token);
            error_log("Token decoded successfully: " . json_encode($decoded));
            
            $userId = $decoded->data->userId;
            $user = $this->userRepository->getById($userId);
            
            if (!$user) {
                error_log("User ID $userId not found in database");
                return new Response(401, json_encode([
                    'error' => 'User not found'
                ]));
            }
            
            error_log("User authenticated successfully: " . json_encode($user));
            $this->request->setUser($user);
            
            return true;
        } catch (\Exception $e) {
            error_log("Token validation error: " . $e->getMessage());
            return new Response(401, json_encode([
                'error' => $e->getMessage()
            ]));
        }
    }
    
    /**
     * Example of another middleware method that could be used
     */
    public function logRequest() {
        // Log request information
        $method = $this->request->getMethod();
        $path = $this->request->getPath();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        // You could log to file or database here
        error_log("[$ip] $method $path");
        
        return true;
    }
}