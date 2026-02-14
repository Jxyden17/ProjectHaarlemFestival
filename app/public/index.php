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
    require __DIR__ . '/../src/Views/shared/error.php';
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
    $cmsService = new App\Service\CmsService($userRepo);

    // Controllers
    $authController = new App\Controllers\AuthController($authService);
    $homeController = new App\Controllers\HomeController();
    $danceController = new App\Controllers\DanceController($scheduleService, $danceService);
    $tourController = new App\Controllers\TourController($pageService);
    $cmsController = new App\Controllers\CmsController($cmsService);

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

        // CMS routes
        $r->addRoute('GET', '/cms', ['CmsController', 'index']);
        $r->addRoute('GET', '/cms/events', ['CmsController', 'eventsIndex']);
        $r->addRoute('GET', '/cms/tickets', ['CmsController', 'ticketsIndex']);
        $r->addRoute('GET', '/cms/users', ['CmsController', 'usersIndex']);
        $r->addRoute('GET', '/cms/users/create', ['CmsController', 'showCreateForm']);
        $r->addRoute('POST', '/cms/users/create', ['CmsController', 'addUser']);
        $r->addRoute('GET', '/cms/users/edit', ['CmsController', 'showEditForm']);
        $r->addRoute('POST', '/cms/users/edit', ['CmsController', 'editUser']);
        $r->addRoute('GET', '/cms/users/delete', ['CmsController', 'showDeleteConfirmation']);
        $r->addRoute('POST', '/cms/users/delete', ['CmsController', 'deleteUser']);
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
                'CmsController' => $cmsController,
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
