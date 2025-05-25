<?php
class Request implements RequestInterface {
    public $user = null;

    public function setUser($user) {
    $this->user = $user;
    }

    public function getUser() {
    return $this->user;
    }
    
    public function getHeader($name) {
    $name = strtoupper(str_replace('-', '_', $name));
    $header = 'HTTP_' . $name;
    
    if (isset($_SERVER[$header])) {
        return $_SERVER[$header];
    }
    
    if (function_exists('getallheaders')) {
        $headers = getallheaders();
        foreach ($headers as $key => $value) {
            if (strtolower($key) === strtolower(str_replace('_', '-', $name))) {
                return $value;
            }
        }
    }
    
    return null;
    }
    public function getMethod(): string {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getPath(): string {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = rtrim($path, '/');
        
        if (empty($path)) {
            return '/';
        }
        
        return $path;
    }

    public function getBody(): array {
        $data = [];
    
        if ($this->getMethod() === 'POST' || $this->getMethod() === 'PUT') {
            try {
                // Get raw input
                $input = file_get_contents('php://input');
                error_log('Raw request body: ' . $input);
                
                if (!empty($input)) {
                    // Try to decode as JSON
                    $jsonData = json_decode($input, true);
                    
                    // Check if JSON was valid
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $data = $jsonData;
                    } else {
                        error_log('JSON decode error: ' . json_last_error_msg());
                        
                        // If not JSON, try to parse as form data
                        parse_str($input, $formData);
                        if (!empty($formData)) {
                            $data = $formData;
                        }
                    }
                }
                
                // If still empty, check $_POST
                if (empty($data) && !empty($_POST)) {
                    $data = $_POST;
                }
            } catch (\Exception $e) {
                error_log('Error parsing request body: ' . $e->getMessage());
            }
        }
    
        return $data;
    }

    public function getAuthorizationHeader(): ?string {
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (isset($headers['Authorization'])) {
                return trim($headers['Authorization']);
            }
        }
        return null;
    }

    public function getBearerToken(): ?string {
        $header = $this->getAuthorizationHeader();
        if ($header && preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }

    
}