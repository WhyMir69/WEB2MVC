<?php
class ApiResponse {
    public static function isApiRequest() {
        $acceptHeader = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
        if (strpos($acceptHeader, 'application/json') !== false) {
            return true;
        }
        
        if (isset($_GET['format']) && $_GET['format'] === 'json') {
            return true;
        }
        
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        if (strpos($userAgent, 'Postman') !== false) {
            return true;
        }
        
        if (isset($_GET['api']) && $_GET['api'] === 'true') {
            return true;
        }
        
        return false;
    }
    
    public static function send($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    public static function sendError($message, $statusCode = 400) {
        self::send(['status' => 'error', 'message' => $message], $statusCode);
    }
    
    public static function sendSuccess($data, $message = null) {
        $response = ['status' => 'success', 'data' => $data];
        if ($message) {
            $response['message'] = $message;
        }
        self::send($response);
    }
}
?>