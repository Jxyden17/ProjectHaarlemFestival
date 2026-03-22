<?php
namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Models\Enums\Event;
use App\Models\Event\VenueModel;
use App\Service\Interfaces\IVenueService;

class CmsVenuesController extends BaseController
{
    private IVenueService $venuesService;

    public function __construct(IVenueService $venuesService)
    {
        $this->venuesService = $venuesService;
    }
    
    public function index(): void
    {
        $this->requireAdmin();
        $selectedEvent = $this->getSelectedEvent();
        $venues = $this->venuesService->getAllVenuesForEvent($selectedEvent->value);

        $this->renderCms('cms/venues/index', [
            'title' => 'Venue Management',
            'venues' => $venues,
            'selectedEvent' => $selectedEvent,
        ]);
    }

    public function showEditForm(): void
    {
        $this->requireAdmin();
        $venue = $this->venuesService->getVenueById((int)($_GET['id'] ?? 0));
        $selectedEvent = $this->getSelectedEvent();
        $this->renderCms('cms/venues/edit', [
            'title' => 'Edit Venue',
            'venue' => $venue,
            'eventTypes' => Event::cases(),
            'selectedEvent' => $selectedEvent,
        ]);
    }

    public function edit(): void
    {
        $this->requireAdmin();
        $selectedEvent = $this->getSelectedEvent();
        $updated = $this->venuesService->updateVenue(new VenueModel(
            id: $_POST['id'],
            eventId: $_POST['event_id'],
            venueName: $_POST['venue_name'],
            address: $_POST['address'],
            venueType: $_POST['venue_type'],
            sessions: [],
            createdAt: null,
        ));
        if($updated){
            $_SESSION['success'] = 'Venue ' . $_POST['venue_name'] . ' edited successfully.';
        } else {
            $_SESSION['error'] = 'Venue ' . $_POST['venue_name'] . ' could not be edited.';
        }
        header('Location: /cms/eventManagement/venues?event_id=' . $selectedEvent->value);
        exit;
    }

    public function showCreateForm(): void
    {
        $this->requireAdmin();
        $selectedEvent = $this->getSelectedEvent();
        $this->renderCms('cms/venues/create', [
            'title' => 'Create Venue',
            'eventTypes' => Event::cases(),
            'selectedEvent' => $selectedEvent,
        ]);
    }

    public function create(): void
    {
        $this->requireAdmin();
        $selectedEvent = $this->getSelectedEvent();
        $created = $this->venuesService->addVenue(new VenueModel(
            id: 0,
            eventId: $_POST['event_id'],
            venueName: $_POST['venue_name'],
            address: $_POST['address'],
            venueType: $_POST['venue_type'],
            sessions: [],
            createdAt: null,
        ));
        if($created){
            $_SESSION['success'] = 'Venue ' . $_POST['venue_name'] . ' created successfully.';
        } else {
            $_SESSION['error'] = 'Venue ' . $_POST['venue_name'] . ' could not be created.';
        }
        header('Location: /cms/eventManagement/venues?event_id=' . $selectedEvent->value);
        exit;
    }

    public function delete(): void
    {
        $this->requireAdmin();
        $selectedEvent = $this->getSelectedEvent();
        $id = (int)($_GET['id'] ?? 0);
        $deleted = $this->venuesService->deleteVenueById($id);
        if ($deleted) {
            $_SESSION['success'] = 'Venue deleted successfully.';
        } else {
            $_SESSION['error'] = 'The specified venue cant by deleted. Try again later.';
        }
        header('Location: /cms/eventManagement/venues?event_id=' . $selectedEvent->value);
        exit;
    }
}