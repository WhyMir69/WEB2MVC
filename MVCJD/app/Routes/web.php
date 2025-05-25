<?php

require_once __DIR__ . '/../../init.php';
require_once 'D:\phpsite\JWT\classes\JWTUtils.php';

$db = new \Core\Database('localhost', 'root', 'stalwart', 'usjr');
$userRepository = new \Repositories\UserRepository($db);
$request = new \Core\Request();
$controller = new \Controllers\UserController($userRepository, $request);
$authMiddleware = new \Middlewares\AuthMiddleware();

return [
    // Public routes
    ['method' => 'GET', 'path' => '/login', 'handler' => function () use ($controller) {
        return $controller->showLoginForm();
    }],
    ['method' => 'POST', 'path' => '/login', 'handler' => function () use ($controller) {
        return $controller->loginUser();
    }],
    ['method' => 'GET', 'path' => '/register', 'handler' => function () use ($controller) {
        return $controller->showRegisterForm();
    }],
    ['method' => 'POST', 'path' => '/register', 'handler' => function () use ($controller) {
        return $controller->registerUser();
    }],

    // Protected routes
    ['method' => 'GET', 'path' => '/user-dashboard', 'handler' => function ($request) use ($controller, $authMiddleware) {
        return $authMiddleware->handle($request, function ($request) use ($controller) {
            return $controller->dashboard();
        });
    }],
    ['method' => 'GET', 'path' => '/users', 'handler' => function ($request) use ($controller, $authMiddleware) {
        return $authMiddleware->handle($request, function ($request) use ($controller) {
            return $controller->getAllUsers();
        });
    }],
    ['method' => 'GET', 'path' => '/users/{id}', 'handler' => function ($id, $request) use ($controller, $authMiddleware) {
        return $authMiddleware->handle($request, function ($request) use ($controller, $id) {
            return $controller->getUserById($id);
        });
    }],
    ['method' => 'POST', 'path' => '/users', 'handler' => function ($request) use ($controller, $authMiddleware) {
        return $authMiddleware->handle($request, function ($request) use ($controller) {
            return $controller->createUser();
        });
    }],
    ['method' => 'POST', 'path' => '/logout', 'handler' => function ($request) use ($controller, $authMiddleware) {
        return $authMiddleware->handle($request, function ($request) use ($controller) {
            return $controller->logoutUser();
        });
    }],
    ['method' => 'PUT', 'path' => '/users/{id}', 'handler' => function ($id, $request) use ($controller, $authMiddleware) {
        return $authMiddleware->handle($request, function ($request) use ($controller, $id) {
            return $controller->updateUser($id);
        });
    }],
    ['method' => 'DELETE', 'path' => '/users/{id}', 'handler' => function ($id, $request) use ($controller, $authMiddleware) {
        return $authMiddleware->handle($request, function ($request) use ($controller, $id) {
            return $controller->deleteUser($id);
        });
    }],
];
