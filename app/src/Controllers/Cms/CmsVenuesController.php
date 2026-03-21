<?php
namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Models\Enums\Event;
use App\Repository\Interfaces\IScheduleRepository;

class CmsVenuesController extends BaseController
{
    private IScheduleRepository $scheduleRepository;

    public function __construct(IScheduleRepository $scheduleRepository)
    {
        $this->scheduleRepository = $scheduleRepository;
    }

    public function index(): void
    {
        $this->requireAdmin();
        $selectedEvent = $this->resolveSelectedEvent();
        $venues = $this->scheduleRepository->getVenuesByEventId($selectedEvent->value);

        $this->renderCms('cms/venues/index', [
            'title' => 'Venue Management',
            'venues' => $venues,
            'selectedEvent' => $selectedEvent,
        ]);
    }

    private function resolveSelectedEvent(): Event
    {
        $slug = strtolower(trim((string)($_GET['event'] ?? '')));
        foreach (Event::cases() as $event) {
            if (strtolower($event->label()) === $slug) {
                return $event;
            }
        }

        return Event::Tour;
    }
}