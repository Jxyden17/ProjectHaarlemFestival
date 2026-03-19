<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Models\Requests\Cms\ScheduleEditorRequest;
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
        $view = $eventName === 'TellingStory' ? 'cms/events/stories-schedule' : 'cms/events/dance-schedule';
        $title = $eventName === 'TellingStory' ? 'Stories Schedule' : $eventName . ' Schedule';

        $this->renderCms($view, [
            'title' => $title,
            'eventName' => $eventName,
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
            $this->cmsScheduleService->saveScheduleData($eventName, $request->toSaveCommand());
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
            $view = $eventName === 'TellingStory' ? 'cms/events/stories-schedule' : 'cms/events/dance-schedule';
            $title = $eventName === 'TellingStory' ? 'Stories Schedule' : $eventName . ' Schedule';

            $this->renderCms($view, [
                'title' => $title,
                'eventName' => $eventName,
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

        $normalizedSlug = strtolower(str_replace(' ', '-', $slug));

        return match ($normalizedSlug) {
            'stories', 'tellingstory', 'telling-story' => 'TellingStory',
            'tour', 'a-stroll-through-history' => 'A Stroll Through History',
            default => ucwords(str_replace('-', ' ', $normalizedSlug)),
        };
    }

    private function toEventSlug(string $eventName): string
    {
        return str_replace(' ', '-', strtolower(trim($eventName)));
    }
}
