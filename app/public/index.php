<?php
session_start();

require __DIR__ . '/../vendor/autoload.php';

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

$showDebug = filter_var($_ENV['APP_DEBUG'] ?? getenv('APP_DEBUG') ?? false, FILTER_VALIDATE_BOOLEAN);
ini_set('display_errors', $showDebug ? '1' : '0');
ini_set('display_startup_errors', $showDebug ? '1' : '0');
error_reporting(E_ALL);

$renderErrorPage = static function (int $statusCode, string $title, string $message, bool $showDebug = false, string $debugError = ''): void {
    http_response_code($statusCode);
    $errorTitle = $title;
    $errorMessage = $message;
    require __DIR__ . '/../src/Views/errors/error.php';
    exit;
};

try {
    // Repositories
    $userRepo = new App\Repository\UserRepository();
    $passwordResetRepo = new App\Repository\PasswordResetRepository();
    $scheduleRepo = new App\Repository\ScheduleRepository();
    $danceRepo = new App\Repository\DanceRepository();
    $pageRepo = new App\Repository\PageRepository();


    // Services
    $mailConfig = App\Models\MailConfig::fromEnvironment();
    $pageService = new App\Service\PageService($pageRepo);
    $mailService = new App\Service\MailService($mailConfig);
    $scheduleService = new App\Service\ScheduleService($scheduleRepo);
    $danceService = new App\Service\DanceService($danceRepo);

    $authService = new App\Service\AuthService($userRepo, $passwordResetRepo, $mailService);
    $adminService = new App\Service\AdminService($userRepo);

    // Controllers
    $authController = new App\Controllers\AuthController($authService);
    $homeController = new App\Controllers\HomeController();
    $danceController = new App\Controllers\DanceController($scheduleService, $danceService);
    $tourController = new App\Controllers\TourController($pageService);
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

        // Dance routes
        $r->addRoute('GET', '/dance', ['DanceController', 'index']);

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
            $renderErrorPage(404, 'Page not found', 'The page you requested does not exist.');

        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $renderErrorPage(405, 'Method not allowed', 'This HTTP method is not allowed for this route.');

        case FastRoute\Dispatcher::FOUND:
            [$controllerName, $method] = $routeInfo[1];
            $vars = $routeInfo[2];

            $controllerMap = [
                'AuthController' => $authController,
                'HomeController' => $homeController,
                'DanceController' => $danceController,
                'TourController' => $tourController,
                'AdminController' => $adminController,
            ];

            if (!isset($controllerMap[$controllerName])) {
                $renderErrorPage(500, 'Application error', 'The requested controller could not be resolved.', $showDebug, "Controller not found: {$controllerName}");
            }

            $controller = $controllerMap[$controllerName];

            // Call the method and pass dynamic route variables
            $controller->$method($vars);
            break;
    }
} catch (\Throwable $e) {
    $debugError = $e->getMessage();
    $renderErrorPage(503, 'Service temporarily unavailable', 'We cannot connect to the database right now. Please try again in a moment.', $showDebug, $debugError
    );
}
