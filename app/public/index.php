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
    $pageMapper = new App\Mapper\PageMapper();
    $scheduleMapper = new App\Mapper\ScheduleMapper($eventMapper);
    $scheduleViewModelMapper = new App\Mapper\ScheduleViewModelMapper();
    $personalProgramMapper = new App\Mapper\PersonalProgramMapper();

    $userRepo = new App\Repository\UserRepository();
    $passwordResetRepo = new App\Repository\PasswordResetRepository();
    $scheduleRepo = new App\Repository\ScheduleRepository($scheduleMapper);
    $danceRepo = new App\Repository\DanceRepository($eventMapper);
    $mediaRepo = new App\Repository\MediaRepository();
    $pageRepo = new App\Repository\PageRepository($pageMapper);
    $jazzRepo = new App\Repository\JazzRepository();
    $yummyRepo = new App\Repository\YummyRepository();
    $personalProgramRepo = new App\Repository\PersonalProgramRepository();
    $artistesRepo = new App\Repository\ArtistesRepository();
    $venueRepo = new App\Repository\VenueRepository();
    $ticketRepo = new App\Repository\TicketRepository();

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
    $imageUploadService = new App\Service\ImageUploadService($mediaRepo, $danceService);
    $audioUploadService = new App\Service\AudioUploadService($mediaRepo, $danceService);
    $yummyService = new App\Service\YummyService($yummyRepo);
    $jazzService = new App\Service\JazzService($jazzRepo, $scheduleRepo);
    $authService = new App\Service\AuthService($userRepo, $passwordResetRepo, $mailService);
    $personalProgramService = new App\Service\PersonalProgramService($personalProgramRepo, $personalProgramMapper);
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $baseUrl = $scheme . '://' . $host;
    $paymentDriver = trim((string) ($_ENV['PAYMENT_DRIVER'] ?? getenv('PAYMENT_DRIVER') ?? 'stripe'));
    $artistesService = new App\Service\ArtistesService($artistesRepo);
    $venueService = new App\Service\VenueService($venueRepo);
    $ticketService = new App\Service\TicketService($ticketRepo);
    $cmsEventManagementService = new App\Service\Cms\CmsEventManagementService();

    $cmsScheduleMapper = new App\Mapper\CmsScheduleMapper();
    $cmsDanceMapper = new App\Mapper\CmsDanceMapper();
    $cmsDanceViewModelMapper = new App\Mapper\CmsDanceViewModelMapper();
    $cmsScheduleService = new App\Service\Cms\CmsScheduleService($scheduleRepo, $cmsScheduleMapper, $cmsScheduleValidator);
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
        $cmsScheduleService,
        $cmsScheduleMapper,
        $cmsPageSaveService,
        $danceService,
        $pageRepo
    );
    $authController = new App\Controllers\AuthController($authService);
    $homeController = new App\Controllers\HomeController($pageService, $scheduleService, $scheduleViewModelMapper);
    $danceController = new App\Controllers\DanceController($danceService, $danceViewModelMapper, $scheduleViewModelMapper);
    $tourController = new App\Controllers\TourController($pageService, $scheduleService, $scheduleViewModelMapper);
    $jazzController = new App\Controllers\JazzController($scheduleService, $jazzService, $scheduleViewModelMapper);
    $yummyController = new App\Controllers\YummyController($yummyService);
    $personalProgramController = new App\Controllers\PersonalProgramController($pageService, $personalProgramService);

    $cmsController = new App\Controllers\Cms\CmsController($cmsService);
    $cmsEventsController = new App\Controllers\Cms\CmsEventsController($cmsService, $danceService, $pageService, $cmsEventEditorService);
    $cmsTicketsController = new App\Controllers\Cms\CmsTicketsController($ticketService);
    $cmsUsersController = new App\Controllers\Cms\CmsUsersController($cmsService);
    $jazzController = new App\Controllers\JazzController($scheduleService, $jazzService, $scheduleViewModelMapper);
    $storiesController = new App\Controllers\StoriesController($pageService, $scheduleService, $scheduleViewModelMapper);
    $cmsEventEditorController = new App\Controllers\Cms\CmsEventEditorController($cmsScheduleService, $cmsEventEditorService);
    $cmsTourContentController = new App\Controllers\Cms\CmsTourContentController($pageService, $cmsEventEditorService);
    $cmsStoriesContentController = new App\Controllers\Cms\CmsStoriesContentController($pageService, $cmsEventEditorService);
    $cmsDanceController = new App\Controllers\Cms\CmsDanceController($cmsDanceService, $cmsDanceViewModelMapper);
    $cmsMediaController = new App\Controllers\Cms\CmsMediaController($imageUploadService, $audioUploadService);
    $cmsHomeContentController = new App\Controllers\Cms\CmsHomeContentController($pageService, $cmsEventEditorService);
    $cmsArtistsController = new App\Controllers\Cms\CmsArtistsController($artistesService);
    $cmsVenuesController = new App\Controllers\Cms\CmsVenuesController($venueService);
    $cmsScheduleController = new App\Controllers\Cms\CmsScheduleController($scheduleService, $venueService, $artistesService);
    $cmsEventManagementController = new App\Controllers\Cms\CmsEventManagementController($cmsEventManagementService);

    // Shopping Cart setup
    $cartRepo = new App\Repository\CartRepository();
    $cartService = new App\Service\CartService($cartRepo);
    $cartController = new App\Controllers\CartController($cartService);
    $bookController = new App\Controllers\BookController($cartService);
    $checkoutRepo = new App\Repository\CheckoutRepository();
    $checkoutService = new App\Service\CheckoutService($cartService, $cartRepo, $checkoutRepo);
    $paymentRepo = new App\Repository\PaymentRepository();
    $paymentService = new App\Service\PaymentService($paymentRepo, $baseUrl, $paymentDriver);
    $checkoutController = new App\Controllers\CheckoutController($checkoutService, $paymentService);
    $paymentController = new App\Controllers\PaymentController($paymentService);



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
        $r->addRoute('GET', '/personal-program', ['PersonalProgramController', 'index']);
        $r->addRoute('POST', '/personal-program/delete', ['PersonalProgramController', 'delete']);


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
        $r->addRoute('GET', '/cms/eventManagement/{eventSlug}/schedule-editor', ['CmsEventEditorController', 'index']);
        $r->addRoute('POST', '/cms/eventManagement/{eventSlug}/schedule-editor', ['CmsEventEditorController', 'update']);
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
        $r->addRoute('GET', '/cms/events/stories-home', ['CmsStoriesContentController', 'index']);
        $r->addRoute('POST', '/cms/events/stories-home', ['CmsStoriesContentController', 'update']);
        $r->addRoute('GET', '/cms/events/stories-details', ['CmsStoriesContentController', 'details']);
        $r->addRoute('POST', '/cms/events/stories-details', ['CmsStoriesContentController', 'detailsUpdate']);
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
        //event management routes
        $r->addRoute('GET', '/cms/eventManagement', ['CmsEventManagementController', 'index']);
        
        $r->addRoute('GET', '/cms/eventManagement/artists', ['CmsArtistsController', 'index']);
        $r->addRoute('GET', '/cms/eventManagement/artists/create', ['CmsArtistsController', 'showCreateForm']);
        $r->addRoute('POST', '/cms/eventManagement/artists/create', ['CmsArtistsController', 'create']);
        $r->addRoute('GET', '/cms/eventManagement/artists/edit', ['CmsArtistsController', 'showEditForm']);
        $r->addRoute('POST', '/cms/eventManagement/artists/edit', ['CmsArtistsController', 'edit']);
        $r->addRoute('GET', '/cms/eventManagement/artists/delete', ['CmsArtistsController', 'delete']);

        $r->addRoute('GET', '/cms/eventManagement/venues', ['CmsVenuesController', 'index']);
        $r->addRoute('GET', '/cms/eventManagement/venues/create', ['CmsVenuesController', 'showCreateForm']);
        $r->addRoute('POST', '/cms/eventManagement/venues/create', ['CmsVenuesController', 'create']);
        $r->addRoute('GET', '/cms/eventManagement/venues/edit', ['CmsVenuesController', 'showEditForm']);
        $r->addRoute('POST', '/cms/eventManagement/venues/edit', ['CmsVenuesController', 'edit']);
        $r->addRoute('GET', '/cms/eventManagement/venues/delete', ['CmsVenuesController', 'delete']);

        $r->addRoute('GET', '/cms/eventManagement/schedules', ['CmsScheduleController', 'index']);
        $r->addRoute('GET', '/cms/eventManagement/schedules/edit', ['CmsScheduleController', 'showEditForm']);
        $r->addRoute('POST', '/cms/eventManagement/schedules/edit', ['CmsScheduleController', 'edit']);
        $r->addRoute('GET', '/cms/eventManagement/schedules/create', ['CmsScheduleController', 'showCreateForm']);
        $r->addRoute('POST', '/cms/eventManagement/schedules/create', ['CmsScheduleController', 'create']);
        $r->addRoute('GET', '/cms/eventManagement/schedules/delete', ['CmsScheduleController', 'delete']);

        $r->addRoute('GET', '/cms/eventManagement/schedules/{sessionId:\d+}/tickets', ['CmsTicketsController', 'index']);
        $r->addRoute('GET', '/cms/eventManagement/schedules/{sessionId:\d+}/tickets/create', ['CmsTicketsController', 'showCreateForm']);
        $r->addRoute('POST', '/cms/eventManagement/schedules/{sessionId:\d+}/tickets/create', ['CmsTicketsController', 'create']);
        $r->addRoute('GET', '/cms/eventManagement/schedules/{sessionId:\d+}/tickets/edit', ['CmsTicketsController', 'showEditForm']);
        $r->addRoute('POST', '/cms/eventManagement/schedules/{sessionId:\d+}/tickets/edit', ['CmsTicketsController', 'edit']);
        $r->addRoute('GET', '/cms/eventManagement/schedules/{sessionId:\d+}/tickets/delete', ['CmsTicketsController', 'delete']);

        $r->addRoute('GET', '/jazz', ['JazzController', 'index']);

        // Shopping Cart routes
        $r->addRoute('GET', '/cart', ['CartController', 'index']);
        $r->addRoute('POST', '/cart/add', ['CartController', 'add']);
        $r->addRoute('POST', '/cart/update', ['CartController', 'update']);
        $r->addRoute('POST', '/cart/remove', ['CartController', 'remove']);
        $r->addRoute('GET', '/book/{sessionId:\d+}', ['BookController', 'index']);
        $r->addRoute('GET', '/checkout', ['CheckoutController', 'index']);
        $r->addRoute('POST', '/checkout/confirm', ['CheckoutController', 'confirm']);
        $r->addRoute('GET', '/payment/return', ['PaymentController', 'return']);
        $r->addRoute('POST', '/payment/webhook', ['PaymentController', 'webhook']);


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
                'PersonalProgramController' => $personalProgramController,
                'CmsEventsController' => $cmsEventsController,
                'CmsTicketsController' => $cmsTicketsController,
                'CmsUsersController' => $cmsUsersController,
                'CmsEventEditorController' => $cmsEventEditorController,
                'CmsDanceController' => $cmsDanceController,
                'CmsTourContentController' => $cmsTourContentController,
                'CmsStoriesContentController' => $cmsStoriesContentController,
                'CmsMediaController' => $cmsMediaController,
                'CmsHomeContentController' => $cmsHomeContentController,
                'CartController' => $cartController,
                'BookController' => $bookController,
                'CheckoutController' => $checkoutController,
                'PaymentController' => $paymentController,
                'CmsArtistsController' => $cmsArtistsController,
                'CmsVenuesController' => $cmsVenuesController,
                'CmsEventManagementController' => $cmsEventManagementController,
                'CmsScheduleController' => $cmsScheduleController
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
