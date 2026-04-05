<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Models\Requests\ScheduleEditorRequest;
use App\Service\Cms\Interfaces\ICmsEventEditorService;
use App\Service\Cms\Interfaces\ICmsScheduleService;

class CmsEventEditorController extends BaseController
{
    private ICmsScheduleService $cmsScheduleService;
    private ICmsEventEditorService $cmsEventEditorService;

    // Stores CMS event editor dependencies so schedule editing actions stay focused on request and response flow.
    public function __construct(ICmsScheduleService $cmsScheduleService, ICmsEventEditorService $cmsEventEditorService)
    {
        $this->cmsScheduleService = $cmsScheduleService;
        $this->cmsEventEditorService = $cmsEventEditorService;
    }

    // Renders the CMS event schedule editor so admins can manage one event's schedule in a single form.
    public function index(array $vars = []): void
    {
        $this->requireAdmin();

        $eventName = 'TellingStory';
        $editorViewModel = $this->cmsEventEditorService->getEditorData($eventName);

        $this->renderCms('cms/events/stories-schedule', [
            'title' => 'Stories Schedule',
            'eventName' => $eventName,
            'editorViewModel' => $editorViewModel,
            'formAction' => '/cms/events/stories/schedule',
            'success' => isset($_GET['saved']),
        ]);
    }

    // Handles the CMS event schedule save so posted schedule rows persist and redirect back with status.
    public function update(array $vars = []): void
    {
        $this->requireAdmin();

        $eventName = 'TellingStory';
        $request = ScheduleEditorRequest::fromArray($_POST);

        try {
            $this->cmsScheduleService->saveScheduleData($eventName, $request->toSaveInput());
            header('Location: /cms/events/stories/schedule?saved=1');
            exit;
        } catch (\Throwable $e) {
            $editorViewModel = $this->cmsEventEditorService->getEditorData($eventName);
            $editorViewModel = $this->cmsEventEditorService->mergePostedEditorData(
                $eventName,
                $editorViewModel,
                $request->venues(),
                $request->performers(),
                $request->sessions()
            );
            $this->renderCms('cms/events/stories-schedule', [
                'title' => 'Stories Schedule',
                'eventName' => $eventName,
                'editorViewModel' => $editorViewModel,
                'formAction' => '/cms/events/stories/schedule',
                'error' => $e->getMessage(),
                'success' => false,
            ]);
        }
    }
}
