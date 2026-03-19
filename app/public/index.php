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
    $eventMapper = new App\Mapper\EventMapper();
    $danceMapper = new App\Mapper\DanceMapper($eventMapper);
    $pageMapper = new App\Mapper\PageMapper();
    $scheduleMapper = new App\Mapper\ScheduleMapper($eventMapper);
    $scheduleViewModelMapper = new App\Mapper\ScheduleViewModelMapper();

    $userRepo = new App\Repository\UserRepository();
    $passwordResetRepo = new App\Repository\PasswordResetRepository();
    $scheduleRepo = new App\Repository\ScheduleRepository($scheduleMapper);
    $danceRepo = new App\Repository\DanceRepository($danceMapper);
    $mediaRepo = new App\Repository\MediaRepository();
    $pageRepo = new App\Repository\PageRepository($pageMapper);
    $jazzRepo = new App\Repository\JazzRepository();
    $yummyRepo = new App\Repository\YummyRepository();

    $mailConfig = App\Models\MailConfig::fromEnvironment();
    $pageService = new App\Service\PageService($pageRepo);
    $cmsPageSaveService = new App\Service\Cms\CmsPageSaveService($pageRepo);
    $mailService = new App\Service\MailService($mailConfig);
    $htmlSanitizerService = new App\Service\HtmlSanitizerService();
    $cmsDanceValidator = new App\Validator\CmsDanceValidator();
    $cmsScheduleValidator = new App\Validator\CmsScheduleValidator();
    $scheduleService = new App\Service\ScheduleService($scheduleRepo, $scheduleMapper);
    $danceService = new App\Service\DanceService($danceRepo, $pageService, $scheduleService);
    $danceViewModelMapper = new App\Mapper\DanceViewModelMapper();
    $imageUploadService = new App\Service\ImageUploadService($mediaRepo, $danceRepo);
    $audioUploadService = new App\Service\AudioUploadService($mediaRepo, $danceRepo);
    $yummyService = new App\Service\YummyService($yummyRepo);
    $jazzService = new App\Service\JazzService($jazzRepo, $scheduleRepo);
    $authService = new App\Service\AuthService($userRepo, $passwordResetRepo, $mailService);

    $cmsScheduleMapper = new App\Mapper\CmsScheduleMapper($danceService);
    $cmsDanceMapper = new App\Mapper\CmsDanceMapper();
    $cmsDanceViewModelMapper = new App\Mapper\CmsDanceViewModelMapper();
    $cmsScheduleService = new App\Service\Cms\CmsScheduleService($scheduleRepo, $cmsScheduleValidator);
    $cmsDanceService = new App\Service\Cms\CmsDanceService(
        $danceRepo,
        $cmsPageSaveService,
        $pageService,
        $htmlSanitizerService,
        $cmsDanceMapper,
        $cmsDanceValidator
    );
    $cmsService = new App\Service\Cms\CmsService($userRepo);
    $cmsEventEditorService = new App\Service\Cms\CmsEventEditorService(
        $scheduleService,
        $cmsScheduleMapper,
        $pageRepo,
        $cmsPageSaveService
    );

    $authController = new App\Controllers\AuthController($authService);
    $homeController = new App\Controllers\HomeController($pageService, $scheduleService, $scheduleViewModelMapper);
    $danceController = new App\Controllers\DanceController($danceService, $danceViewModelMapper, $scheduleViewModelMapper);
    $tourController = new App\Controllers\TourController($pageService, $scheduleService, $scheduleViewModelMapper);
    $jazzController = new App\Controllers\JazzController($scheduleService, $jazzService, $scheduleViewModelMapper);
    $yummyController = new App\Controllers\YummyController($yummyService);

    $cmsController = new App\Controllers\Cms\CmsController($cmsService);
    $cmsEventsController = new App\Controllers\Cms\CmsEventsController($cmsService, $danceService);
    $cmsTicketsController = new App\Controllers\Cms\CmsTicketsController($cmsService);
    $cmsUsersController = new App\Controllers\Cms\CmsUsersController($cmsService);
    $storiesController = new App\Controllers\StoriesController($pageService, $scheduleService, $scheduleViewModelMapper);
    $cmsDanceController = new App\Controllers\Cms\CmsDanceController($cmsDanceService, $cmsDanceViewModelMapper);
    $cmsEventEditorController = new App\Controllers\Cms\CmsEventEditorController($cmsScheduleService, $cmsEventEditorService);
    $cmsMediaController = new App\Controllers\Cms\CmsMediaController($imageUploadService, $audioUploadService);
    $cmsTourContentController = new App\Controllers\Cms\CmsTourContentController($pageService, $cmsEventEditorService);
    $cmsHomeContentController = new App\Controllers\Cms\CmsHomeContentController($pageService, $cmsEventEditorService);

    $dispatcher = simpleDispatcher(function (RouteCollector $r) {
        $r->addRoute('GET', '/', ['HomeController', 'index']);

        $r->addRoute('GET', '/login', ['AuthController', 'showLogin']);
        $r->addRoute('POST', '/login', ['AuthController', 'login']);
        $r->addRoute('GET', '/register', ['AuthController', 'showRegister']);
        $r->addRoute('POST', '/register', ['AuthController', 'register']);
        $r->addRoute('GET', '/forgot-password', ['AuthController', 'showForgotPassword']);
        $r->addRoute('POST', '/forgot-password', ['AuthController', 'sendPasswordResetLink']);
        $r->addRoute('GET', '/reset-password', ['AuthController', 'showResetPassword']);
        $r->addRoute('POST', '/reset-password', ['AuthController', 'resetPassword']);
        $r->addRoute('GET', '/logout', ['AuthController', 'logout']);

        $r->addRoute('GET', '/tour', ['TourController', 'index']);
        $r->addRoute('GET', '/tour/details', ['TourController', 'details']);

        $r->addRoute('GET', '/dance', ['DanceController', 'index']);
        $r->addRoute('GET', '/dance/{pageSlug}', ['DanceController', 'detail']);

        $r->addRoute('GET', '/yummy', ['YummyController', 'index']);
        $r->addRoute('GET', '/yummy/{slug}', ['YummyController', 'restaurant']);

        //Stories Routes
        $r->addRoute('GET', '/stories', ['StoriesController', 'index']);
        $r->addRoute('GET', '/stories/details', ['StoriesController', 'details']);
        $r->addRoute('GET', '/stories/{slug}', ['StoriesController', 'details']);

        // CMS routes
        $r->addRoute('GET', '/cms', ['CmsController', 'index']);
        $r->addRoute('GET', '/cms/events', ['CmsEventsController', 'index']);
        $r->addRoute('GET', '/cms/events/{eventSlug}/schedule', ['CmsEventEditorController', 'index']);
        $r->addRoute('POST', '/cms/events/{eventSlug}/schedule', ['CmsEventEditorController', 'update']);
        $r->addRoute('GET', '/cms/events/dance-home', ['CmsDanceController', 'index']);
        $r->addRoute('POST', '/cms/events/dance-home', ['CmsDanceController', 'updateHome']);
        $r->addRoute('POST', '/cms/events/dance-homeAPI', ['CmsDanceController', 'updateHomeAPI']);
        $r->addRoute('GET', '/cms/events/dance-detail/{pageSlug}', ['CmsDanceController', 'detail']);
        $r->addRoute('POST', '/cms/events/dance-detail/{pageSlug}', ['CmsDanceController', 'updateDetail']);
        $r->addRoute('POST', '/cms/events/dance-detail/{pageSlug}/updateAPI', ['CmsDanceController', 'updateDetailAPI']);
        $r->addRoute('GET', '/cms/events/tour-home', ['CmsTourContentController', 'index']);
        $r->addRoute('POST', '/cms/events/tour-home', ['CmsTourContentController', 'update']);
        $r->addRoute('GET', '/cms/events/tour-details', ['CmsTourContentController', 'details']);
        $r->addRoute('POST', '/cms/events/tour-details', ['CmsTourContentController', 'detailsUpdate']);
        $r->addRoute('POST', '/cms/media/upload-image', ['CmsMediaController', 'uploadImage']);
        $r->addRoute('POST', '/cms/media/upload-audio', ['CmsMediaController', 'uploadAudio']);
        $r->addRoute('GET', '/cms/tickets', ['CmsTicketsController', 'index']);
        $r->addRoute('GET', '/cms/users', ['CmsUsersController', 'index']);
        $r->addRoute('GET', '/cms/users/create', ['CmsUsersController', 'showCreateForm']);
        $r->addRoute('POST', '/cms/users/create', ['CmsUsersController', 'addUser']);
        $r->addRoute('GET', '/cms/users/edit', ['CmsUsersController', 'showEditForm']);
        $r->addRoute('POST', '/cms/users/edit', ['CmsUsersController', 'editUser']);
        $r->addRoute('GET', '/cms/users/delete', ['CmsUsersController', 'showDeleteConfirmation']);
        $r->addRoute('POST', '/cms/users/delete', ['CmsUsersController', 'deleteUser']);
        $r->addRoute('GET', '/cms/events/home', ['CmsHomeContentController', 'index']);
        $r->addRoute('POST', '/cms/events/home', ['CmsHomeContentController', 'update']);

        $r->addRoute('GET', '/jazz', ['JazzController', 'index']);
    });

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
                'YummyController' => $yummyController,
                'CmsController' => $cmsController,
                'JazzController' => $jazzController,
                'StoriesController' => $storiesController,
                'CmsEventsController' => $cmsEventsController,
                'CmsTicketsController' => $cmsTicketsController,
                'CmsUsersController' => $cmsUsersController,
                'CmsEventEditorController' => $cmsEventEditorController,
                'CmsDanceController' => $cmsDanceController,
                'CmsTourContentController' => $cmsTourContentController,
                'CmsMediaController' => $cmsMediaController,
                'CmsHomeContentController' => $cmsHomeContentController,
            ];

            if (!isset($controllerMap[$controllerName])) {
                $renderErrorPage(
                    500,
                    'Application error',
                    'The requested controller could not be resolved.',
                    $showDebug,
                    "Controller not found: {$controllerName}"
                );
            }

            $controller = $controllerMap[$controllerName];
            $controller->$method($vars);
            break;
    }
} catch (\Throwable $e) {
    if ($showDebug) {
        var_dump($e);
    }
    $debugError = $e->getMessage();
    $renderErrorPage(
        503,
        'Service temporarily unavailable',
        'We cannot connect to the database right now. Please try again in a moment.',
        $showDebug,
        $debugError
    );
}
