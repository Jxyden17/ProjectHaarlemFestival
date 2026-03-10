<?php

namespace App\Service\Cms;

use App\Mapper\CmsScheduleMapper;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorViewModel;
use App\Service\Cms\Interfaces\ICmsEventEditorService;
use App\Service\Interfaces\IScheduleService;

class CmsEventEditorService implements ICmsEventEditorService
{
    private IScheduleService $scheduleService;
    private CmsScheduleMapper $cmsScheduleMapper;

    public function __construct(IScheduleService $scheduleService, CmsScheduleMapper $cmsScheduleMapper)
    {
        $this->scheduleService = $scheduleService;
        $this->cmsScheduleMapper = $cmsScheduleMapper;
    }

    public function getEditorData(string $eventName): ScheduleEditorViewModel
    {
        $editorViewModel = $this->scheduleService->getScheduleEditorData($eventName);
        if (strtolower($eventName) !== 'dance') {
            return $editorViewModel;
        }

        return new ScheduleEditorViewModel(
            $editorViewModel->eventName,
            $editorViewModel->venues,
            $this->cmsScheduleMapper->applyDanceArtistImageMetadata($eventName, $editorViewModel->performers),
            $editorViewModel->sessions
        );
    }

    public function mergePostedEditorData(
        string $eventName,
        ScheduleEditorViewModel $editorData,
        array $postedVenues,
        array $postedPerformers,
        array $postedSessions
    ): ScheduleEditorViewModel {
        $venues = $editorData->venues;
        if (!empty($postedVenues)) {
            $venues = $this->cmsScheduleMapper->mapVenueViewModels($postedVenues);
        }

        $performers = $editorData->performers;
        if (!empty($postedPerformers)) {
            $existingPerformers = strtolower($eventName) === 'dance' ? $editorData->performers : [];
            $performers = $this->cmsScheduleMapper->mapPerformerViewModels($postedPerformers, $existingPerformers);
        }

        $sessions = $editorData->sessions;
        if (!empty($postedSessions)) {
            $sessions = $this->cmsScheduleMapper->mapSessionViewModels($postedSessions);
        }

        return new ScheduleEditorViewModel($editorData->eventName, $venues, $performers, $sessions);
    }
}
