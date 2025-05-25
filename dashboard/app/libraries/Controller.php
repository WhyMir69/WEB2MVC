<?php
// Make sure to include the ApiResponse class
require_once 'ApiResponse.php';

/**
 * Base Controller
 * Loads models and views
 */
class Controller {
    public function model($model) {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }
    
    // Load view
    public function view($view, $data = []) {
        if (ApiResponse::isApiRequest()) {
            ApiResponse::sendSuccess($data);
        }
        
        if (file_exists('../app/views/' . $view . '.php')) {
            require_once '../app/views/' . $view . '.php';
        } else {
            die('View does not exist');
        }
    }
}
?>