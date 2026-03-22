<?php 

namespace App\Controllers\Cms;


use App\Controllers\BaseController;
use App\Service\Interfaces\IScheduleService;
use App\Models\Enums\Event;
use App\Models\Enums\Language;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorViewModel;

class CmsScheduleController extends BaseController
{
    private IScheduleService $scheduleService;

    public function __construct(IScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
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
        $this->renderCms('cms/schedule/edit', ['title' => 'Edit Schedule', 'selectedEvent' => $selectedEvent, 'schedule' => $scheduleData, 'eventTypes' => Event::cases(), 'language' => $language]);
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
            language: $_POST['language_id']
         );
        
        if ($updated) {
            $_SESSION['success'] = 'Schedule updated successfully.';
        } else {
            $_SESSION['error'] = 'Schedule could not be updated.';
        }
        header('Location: /cms/eventManagement/schedules?event_id=' . $selectedEvent->value);
        exit;
    }
}   