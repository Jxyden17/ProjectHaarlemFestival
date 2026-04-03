<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Models\Enums\Event;
use App\Service\Interfaces\IArtistesService;
use App\Service\Cms\Interfaces\ICmsEventEditorService;
use App\Models\Event\PerformerModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorPerformerRowViewModel;

class CmsArtistsController extends BaseController
{
    private IArtistesService $artistesService;
    private ICmsEventEditorService $cmsEventEditorService;

    public function __construct(IArtistesService $artistesService, ICmsEventEditorService $cmsEventEditorService)
    {
        $this->artistesService = $artistesService;
        $this->cmsEventEditorService = $cmsEventEditorService;
    }

    public function index(): void
    {
        $this->requireAdmin();
        try
        {
            $selectedEvent = $this->getSelectedEvent();
            $artistes = $this->artistesService->getAllArtistesForEvent((int)$selectedEvent->value);
            $danceArtistMediaById = [];

            if ($selectedEvent === Event::Dance) {
                $editorData = $this->cmsEventEditorService->getEditorData('Dance');

                foreach ($editorData->performers as $performer) {
                    if (!$performer instanceof ScheduleEditorPerformerRowViewModel) {
                        continue;
                    }

                    $danceArtistMediaById[$performer->id] = [
                        'artistSectionItemId' => $performer->artistSectionItemId,
                        'artistImagePath' => $performer->artistImagePath,
                    ];
                }
            }

            $this->renderCms('cms/artists/index', [
                'title' => 'Artist Management',
                'artistes' => $artistes,
                'selectedEvent' => $selectedEvent,
                'danceArtistMediaById' => $danceArtistMediaById,
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
        $artiste = $this->artistesService->getArtisteById($_GET['id'] ?? 0);
        $selectedEvent = $this->getSelectedEvent();
        $this->renderCms('cms/artists/edit', [
            'title' => 'Edit Artist',
            'artiste' => $artiste,
            'eventTypes' => Event::cases(),
            'selectedEvent' => $selectedEvent,
        ]);
    }

    public function edit(): void
    {
        $this->requireAdmin();
        $selectedEvent = $this->getSelectedEvent();
        $edited = $this->artistesService->updateArtiste(new PerformerModel(
            id: (int)($_POST['id'] ?? 0),
            eventId: (int)($_POST['event_id'] ?? 0),
            performerName: $_POST['performer_name'] ?? '',
            performerType: $_POST['performer_type'] ?? '',
            description: $_POST['description'] ?? '',
            createdAt: $_POST['created_at'] ?? null,
        ));
         if($edited){
            $_SESSION['success'] = 'Artist ' . ($_POST['performer_name'] ?? '') . ' edited successfully.';
        } else {
            $_SESSION['error'] = 'Artist ' . ($_POST['performer_name'] ?? '') . ' could not be edited.';
        }
        header('Location: /cms/eventManagement/artists?event_id=' . $selectedEvent->value);
        exit();
    }

    public function showCreateForm(): void
    {
        $this->requireAdmin();
        $selectedEvent = $this->getSelectedEvent();
        $this->renderCms('cms/artists/create', [
            'title' => 'Create Artist',
            'eventTypes' => Event::cases(),
            'selectedEvent' => $selectedEvent,
        ]);
    }

    public function create(): void
    {
        $this->requireAdmin();
        $selectedEvent = $this->getSelectedEvent();
        $created = $this->artistesService->addArtiste(new PerformerModel(
            id: 0,
            eventId: (int)($_POST['event_id'] ?? 0),
            performerName: $_POST['performer_name'] ?? '',
            performerType: $_POST['performer_type'] ?? '',
            description: $_POST['description'] ?? '',
            createdAt: date('Y-m-d H:i:s'),
        ));
        if($created){
            $_SESSION['success'] = 'Artist ' . ($_POST['performer_name'] ?? '') . ' created successfully.';
        } else {
            $_SESSION['error'] = 'Artist ' . ($_POST['performer_name'] ?? '') . ' could not be created.';
        }
        header('Location: /cms/eventManagement/artists?event_id=' . $selectedEvent->value);
        exit();
    }

    public function delete(): void
    {
        $this->requireAdmin();
        $selectedEvent = $this->getSelectedEvent();
        $deleted = $this->artistesService->deleteArtisteById((int)($_GET['id'] ?? 0));
        if($deleted){
            $_SESSION['success'] = 'Artist ' . ($_POST['performer_name'] ?? '') . ' deleted successfully.';
        } else {
            $_SESSION['error'] = 'Artist ' . $_POST['performer_name'] . ' could not be deleted.';
        }
        header('Location: /cms/eventManagement/artists?event_id=' . $selectedEvent->value);
        exit();
    }
}
