<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Models\Event\EventModel;
use App\Service\Cms\Interfaces\IArtistesService;
use App\Models\Enums\Event;
use App\Models\Event\PerformerModel;

class CmsArtistsController extends BaseController
{
    private IArtistesService $artistesService;

    public function __construct(IArtistesService $artistesService)
    {
        $this->artistesService = $artistesService;
    }

    public function index(): void
    {
        $this->requireAdmin();
        try
        {
            $selectedEvent = $this->getSelectedEvent();
            $artistes = $this->artistesService->getAllArtistesForEvent((int)$selectedEvent->value);
            $this->renderCms('cms/artists/index', [
                'title' => 'Artist Management',
                'artistes' => $artistes,
            ]);
        } 
        catch (\InvalidArgumentException $e) 
        {
            $this->noPageFounded('Invalid Event', 'The specified event is invalid or missing.');
            return;
        }
    }

    public function showEditForm(): void
    {
        $this->requireAdmin();
        $artiste = $this->artistesService->getArtisteById((int)($_GET['id'] ?? 0));
        Event::cases();
        $this->renderCms('cms/artists/edit', [
            'title' => 'Edit Artist',
            'artiste' => $artiste,
            'eventTypes' => Event::cases(),
        ]);
    }

    public function edit(): void
    {
        $this->requireAdmin();
        (int)$selectedEvent = $this->getSelectedEvent();
        try
        {
        $this->artistesService->updateArtiste(new PerformerModel(
            id: (int)($_POST['id'] ?? 0),
            eventId: (int)($_POST['event_id'] ?? 0),
            performerName: $_POST['performer_name'] ?? '',
            performerType: $_POST['performer_type'] ?? '',
            description: $_POST['description'] ?? '',
            createdAt: $_POST['created_at'] ?? null,
        ));
        header('Location: /cms/artists?event=' . rawurlencode(strtolower($selectedEvent->label())));
        }
        catch (\Exception $e)
        {
            error_log($e->getMessage());
            $this->renderCms('cms/artists', ['title' => 'Edit Artist', 'error' => 'Failed to update artist.']);
            return;
        }
    }

    public function showCreateForm(): void
    {
        $this->requireAdmin();
        (int)$selectedEvent = $this->getSelectedEvent();
        $eventsTypes = Event::cases();
        $this->renderCms('cms/artists/create', [
            'title' => 'Create Artist',
            'eventTypes' => $eventsTypes,
            'selectedEvent' => $selectedEvent,
        ]);
    }

    public function create(): void
    {
        $this->requireAdmin();
        (int)$selectedEvent = $this->getSelectedEvent();
        $this->artistesService->addArtiste(new PerformerModel(
            id: 0,
            eventId: (int)($_POST['event_id'] ?? 0),
            performerName: $_POST['performer_name'] ?? '',
            performerType: $_POST['performer_type'] ?? '',
            description: $_POST['description'] ?? '',
            createdAt: date('Y-m-d H:i:s'),
        ));
        header('Location: /cms/artists?event=' . rawurlencode(strtolower($selectedEvent->label())));
    }

    public function delete(): void
    {
        $this->requireAdmin();
        (int)$selectedEvent = $this->getSelectedEvent();
        $this->artistesService->deleteArtisteById((int)($_GET['id'] ?? 0));
        header('Location: /cms/artists?event=' . rawurlencode(strtolower($selectedEvent->label())));
    }
}