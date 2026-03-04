<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Models\Requests\Cms\ScheduleEditorRequest;
use App\Service\Interfaces\ICmsEventEditorService;
use App\Service\Interfaces\IScheduleService;

class CmsEventEditorController extends BaseController
{
    private IScheduleService $scheduleService;
    private ICmsEventEditorService $cmsEventEditorService;

    public function __construct(IScheduleService $scheduleService, ICmsEventEditorService $cmsEventEditorService)
    {
        $this->scheduleService = $scheduleService;
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
            $this->scheduleService->saveScheduleData($eventName, $request->toSaveCommand());
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
