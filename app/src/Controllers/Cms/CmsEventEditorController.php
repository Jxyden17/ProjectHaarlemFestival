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
        $editorViewModel = $this->cmsEventEditorService->getEditorData($eventName);
        $view = $eventName === 'TellingStory' ? 'cms/events/stories-schedule' : 'cms/events/dance-schedule';
        $title = $eventName === 'TellingStory' ? 'Stories Schedule' : $eventName . ' Schedule';
        $tourDetailPages = $view === 'cms/events/dance-schedule'
            ? $this->cmsEventEditorService->getTourDetailPages()
            : [];

        $this->renderCms($view, [
            'title' => $title,
            'eventName' => $eventName,
            'editorViewModel' => $editorViewModel,
            'formAction' => $this->buildEventManagementScheduleEditorPath($eventName),
            'success' => isset($_GET['saved']),
            'tourDetailPages' => $tourDetailPages,
            'backUrl' => '/cms/eventManagement',
        ]);
    }

    public function update(array $vars = []): void
    {
        $this->requireAdmin();

        $eventName = $this->resolveEventName($vars);
        $request = ScheduleEditorRequest::fromArray($_POST);

        try {
            $this->cmsScheduleService->saveScheduleData($eventName, $request->toSaveInput());
            header('Location: ' . $this->buildEventManagementScheduleEditorPath($eventName) . '?saved=1');
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
            $tourDetailPages = $view === 'cms/events/dance-schedule'
                ? $this->cmsEventEditorService->getTourDetailPages()
                : [];

            $this->renderCms($view, [
                'title' => $title,
                'eventName' => $eventName,
                'editorViewModel' => $editorViewModel,
                'formAction' => $this->buildEventManagementScheduleEditorPath($eventName),
                'error' => $e->getMessage(),
                'success' => false,
                'tourDetailPages' => $tourDetailPages,
                'backUrl' => '/cms/eventManagement',
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

    private function buildEventManagementScheduleEditorPath(string $eventName): string
    {
        return '/cms/eventManagement/' . $this->toEventSlug($eventName) . '/schedule-editor';
    }
}
