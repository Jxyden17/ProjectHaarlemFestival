<?php
session_start();

require __DIR__ . '/../vendor/autoload.php';

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

// Repositories
$userRepo = new App\Repository\UserRepository();
$passwordResetRepo = new App\Repository\PasswordResetRepository();

// Services
$mailConfig = App\Models\MailConfig::fromEnvironment();
$mailService = new App\Service\MailService($mailConfig);
$authService = new App\Service\AuthService($userRepo, $passwordResetRepo, $mailService);

// Controllers
$authController = new App\Controllers\AuthController($authService);
$homeController = new App\Controllers\HomeController();
$historyController = new App\Controllers\HistoryController();

// Routes
$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    // Home route
    $r->addRoute('GET', '/', ['HomeController', 'index']);

    // Auth routes
    $r->addRoute('GET', '/login', ['AuthController', 'showLogin']);
    $r->addRoute('POST', '/login', ['AuthController', 'login']);
    $r->addRoute('GET', '/register', ['AuthController', 'showRegister']);
    $r->addRoute('POST', '/register', ['AuthController', 'register']);
    $r->addRoute('GET', '/forgot-password', ['AuthController', 'showForgotPassword']);
    $r->addRoute('POST', '/forgot-password', ['AuthController', 'sendPasswordResetLink']);
    $r->addRoute('GET', '/reset-password', ['AuthController', 'showResetPassword']);
    $r->addRoute('POST', '/reset-password', ['AuthController', 'resetPassword']);
    $r->addRoute('GET', '/logout', ['AuthController', 'logout']);
    $r->addRoute('GET', '/history', ['HistoryController', 'index']);
});

// Dispatch request
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = strtok($_SERVER['REQUEST_URI'], '?');
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo 'Not Found';
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo 'Method Not Allowed';
        break;

    case FastRoute\Dispatcher::FOUND:
        [$controllerName, $method] = $routeInfo[1];
        $vars = $routeInfo[2];

        $controllerMap = [
            'AuthController' => $authController,
            'HomeController' => $homeController,
            'HistoryController' => $historyController,
        ];

        if (!isset($controllerMap[$controllerName])) {
            http_response_code(500);
            echo "Controller not found: $controllerName";
            exit;
        }

        $controller = $controllerMap[$controllerName];

        // Call the method and pass dynamic route variables
        $controller->$method($vars);
        break;
}
