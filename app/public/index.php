<?php
session_start();

require __DIR__ . '/../vendor/autoload.php';

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

// Repositories
$userRepo = new App\Repository\UserRepository();
$pageRepo = new App\Repository\PageRepository();
$passwordResetRepo = new App\Repository\PasswordResetRepository();

// Services
$pageService = new App\Service\PageService($pageRepo);

$mailConfig = App\Models\MailConfig::fromEnvironment();
$mailService = new App\Service\MailService($mailConfig);

$authService = new App\Service\AuthService($userRepo, $passwordResetRepo, $mailService);;
$adminService = new App\Service\AdminService($userRepo);

// Controllers
$authController = new App\Controllers\AuthController($authService);
$homeController = new App\Controllers\HomeController();
$tourController = new App\Controllers\TourController($pageService);
$historyController = new App\Controllers\HistoryController();
$adminController = new App\Controllers\AdminController($adminService);

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

    //Tour
    $r->addRoute('GET', '/tour', ['TourController', 'index']);
    $r->addRoute('GET', '/tour/details', ['TourController', 'details']);
    
    // History route
    $r->addRoute('GET', '/history', ['HistoryController', 'index']);

    // Admin routes
    $r->addRoute('GET', '/users', ['AdminController', 'index']);
    $r->addRoute('GET', '/admin/users/edit', ['AdminController', 'showEditForm']);
    $r->addRoute('POST', '/admin/users/edit', ['AdminController', 'editUser']);
    $r->addRoute('GET', '/admin/users/delete', ['AdminController', 'showDeleteConfirmation']);
    $r->addRoute('POST', '/admin/users/delete', ['AdminController', 'deleteUser']);
    $r->addRoute('GET', '/admin/users/create', ['AdminController', 'showCreateForm']);
    $r->addRoute('POST', '/admin/users/create', ['AdminController', 'addUser']);
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
            'TourController' => $tourController,
            'HistoryController' => $historyController,
            'AdminController' => $adminController,
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