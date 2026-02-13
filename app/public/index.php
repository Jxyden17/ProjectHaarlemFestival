<?php
session_start();

require __DIR__ . '/../vendor/autoload.php';

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

// Repositories
$userRepo = new App\Repository\UserRepository();
$pageRepo = new App\Repository\PageRepository();

// Services
$authService = new App\Service\AuthService($userRepo);
$pageService = new App\Service\PageService($pageRepo);

// Controllers
$authController = new App\Controllers\AuthController($authService);
$homeController = new App\Controllers\HomeController();
$tourController = new App\Controllers\TourController($pageService);

// Routes
$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    // Home route
    $r->addRoute('GET', '/', ['HomeController', 'index']);

    // Auth routes
    $r->addRoute('GET', '/login', ['AuthController', 'showLogin']);
    $r->addRoute('POST', '/login', ['AuthController', 'login']);
    $r->addRoute('GET', '/register', ['AuthController', 'showRegister']);
    $r->addRoute('POST', '/register', ['AuthController', 'register']);
    $r->addRoute('GET', '/logout', ['AuthController', 'logout']);

    //Tour
    $r->addRoute('GET', '/tour', ['TourController', 'index']);
    $r->addRoute('GET', '/tour/details', ['TourController', 'details']);
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
