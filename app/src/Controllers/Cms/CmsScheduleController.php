<?php 

namespace App\Controllers\Cms;


use App\Controllers\BaseController;
use App\Service\Interfaces\IScheduleService;
use App\Service\Interfaces\IVenueService;
use App\Service\Interfaces\IArtistesService;
use App\Models\Enums\Event;
use App\Models\Enums\Language;

class CmsScheduleController extends BaseController
{
    private IScheduleService $scheduleService;
    private IVenueService $venueService;
    private IArtistesService $artistesService;

    public function __construct(IScheduleService $scheduleService, IVenueService $venueService, IArtistesService $artistesService)
    {
        $this->scheduleService = $scheduleService;
        $this->venueService = $venueService;
        $this->artistesService = $artistesService;

    }

    public function index(): void
    {
        $this->requireAdmin();
        $selectedEvent = $this->getSelectedEvent();
        $schedules = $this->scheduleService->getScheduleDataForEvent($selectedEvent->dbName(), $selectedEvent->label());
        $this->renderCms('cms/schedule/index', ['title' => 'Schedule Management', 'selectedEvent' => $selectedEvent, 'eventTypes' => Event::cases(), 'schedules' => $schedules]);
    }

    public function showEditForm(): void
    {
        $this->requireAdmin();
        $selectedEvent = $this->getSelectedEvent();
        $language = Language::cases();
        $id = (int)($_GET['id'] ?? 0);
        $scheduleData = $this->scheduleService->getSessionById($id);
         if (!$scheduleData) {
            http_response_code(404);
            $this->render('shared/error', [
                'errorTitle' => 'Schedule not found',
                'errorMessage' => 'The schedule you requested does not exist.',
            ]);
            return;
        }
        $venueData = $this->venueService->getAllVenuesForEvent($selectedEvent->value);
        $performers = $this->artistesService->getAllArtistesForEvent((int)$selectedEvent->value);
        $this->renderCms('cms/schedule/edit', ['title' => 'Edit Schedule', 'selectedEvent' => $selectedEvent, 'schedule' => $scheduleData, 'language' => $language, 'venues' => $venueData, 'performers' => $performers]);
    }

    public function edit(): void
    {
        $selectedEvent = $this->getSelectedEvent();
        $updated = $this->scheduleService->editSchedule(
            id: $_POST['id'],
            eventId: $_POST['event_id'],
            venueId: $_POST['venue_id'],
            date: $_POST['date'],
            startTime: $_POST['start_time'],
            availableSpots: $_POST['available_spots'],
            label: trim($_POST['label']),
            price: (float)$_POST['price'],
            language: $_POST['language_id'],
            performerIds: $_POST['performer_ids'] ?? []
         );
        
        if ($updated) {
            $_SESSION['success'] = 'Schedule updated successfully.';
        } else {
            $_SESSION['error'] = 'Schedule could not be updated.';
        }
        header('Location: /cms/eventManagement/schedules?event_id=' . $selectedEvent->value);
        exit;
    }

    public function showCreateForm(): void
    {
        $this->requireAdmin();
        $selectedEvent = $this->getSelectedEvent();
        $language = Language::cases();
        $venueData = $this->venueService->getAllVenuesForEvent($selectedEvent->value);
        $performers = $this->artistesService->getAllArtistesForEvent((int)$selectedEvent->value);
        $this->renderCms('cms/schedule/create', ['title' => 'Create Schedule', 'selectedEvent' => $selectedEvent, 'venues' => $venueData, 'performers' => $performers, 'language' => $language]);
    }

    public function create(): void
    {
        $selectedEvent = $this->getSelectedEvent();
        $created = $this->scheduleService->createSchedule(
            eventId: $_POST['event_id'],
            venueId: $_POST['venue_id'],
            date: $_POST['date'],
            startTime: $_POST['start_time'],
            availableSpots: $_POST['available_spots'],
            label: trim($_POST['label']),
            price: (float)$_POST['price'],
            language: $_POST['language_id'],
            performerIds: $_POST['performer_ids'] ?? []
        );
        
        if ($created) {
            $_SESSION['success'] = 'Schedule created successfully.';
        } else {
            $_SESSION['error'] = 'Schedule could not be created.';
        }
        header('Location: /cms/eventManagement/schedules?event_id=' . $selectedEvent->value);
        exit;
    }

    public function delete(): void
    {
        $selectedEvent = $this->getSelectedEvent();
        $id = ($_GET['id']);
        $deleted = $this->scheduleService->deleteSchedule($id);
        
        if ($deleted) {
            $_SESSION['success'] = 'Schedule deleted successfully.';
        } else {
            $_SESSION['error'] = 'Schedule could not be deleted.';
        }
        header('Location: /cms/eventManagement/schedules?event_id=' . $selectedEvent->value);
        exit;
    }
}   
