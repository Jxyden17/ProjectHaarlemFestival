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

    public function __construct(ICmsScheduleService $cmsScheduleService, ICmsEventEditorService $cmsEventEditorService)
    {
        $this->cmsScheduleService = $cmsScheduleService;
        $this->cmsEventEditorService = $cmsEventEditorService;
    }

    public function index(array $vars = []): void
    {
        $this->requireAdmin();

        $eventName = $this->resolveEventName($vars);
        $eventSlug = $this->toEventSlug($eventName);
        $editorViewModel = $this->cmsEventEditorService->getEditorData($eventName);
        $this->renderCms('cms/events/dance-schedule', [
            'title' => $eventName . ' Schedule',
            'editorViewModel' => $editorViewModel,
            'formAction' => '/cms/events/' . $eventSlug . '/schedule',
            'success' => isset($_GET['saved']),
        ]);
    }

    public function update(array $vars = []): void
    {
        $this->requireAdmin();

        $eventName = $this->resolveEventName($vars);
        $eventSlug = $this->toEventSlug($eventName);
        $request = ScheduleEditorRequest::fromArray($_POST);

        try {
            $this->cmsScheduleService->saveScheduleData($eventName, $request->toSaveInput());
            header('Location: /cms/events/' . $eventSlug . '/schedule?saved=1');
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

            $this->renderCms('cms/events/dance-schedule', [
                'title' => $eventName . ' Schedule',
                'editorViewModel' => $editorViewModel,
                'formAction' => '/cms/events/' . $eventSlug . '/schedule',
                'error' => $e->getMessage(),
                'success' => false,
            ]);
        }
    }

    private function resolveEventName(array $vars): string
    {
        $slug = trim((string)($vars['eventSlug'] ?? ''));
        if ($slug === '') {
            throw new \InvalidArgumentException('Event slug is required.');
        }

        $name = str_replace('-', ' ', strtolower($slug));
        return ucwords($name);
    }

    private function toEventSlug(string $eventName): string
    {
        return str_replace(' ', '-', strtolower(trim($eventName)));
    }
}
