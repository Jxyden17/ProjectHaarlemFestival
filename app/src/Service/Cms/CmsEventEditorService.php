<?php

namespace App\Service\Cms;

use App\Models\Page\SectionItem;
use App\Models\Requests\Cms\Schedule\SchedulePerformerRowRequest;
use App\Models\Requests\Cms\Schedule\ScheduleSessionRowRequest;
use App\Models\Requests\Cms\Schedule\ScheduleVenueRowRequest;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorPerformerRowViewModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorSessionRowViewModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorVenueRowViewModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorViewModel;
use App\Service\Cms\Interfaces\ICmsEventEditorService;
use App\Service\Interfaces\IDanceService;
use App\Service\Interfaces\IScheduleService;

class CmsEventEditorService implements ICmsEventEditorService
{
    private IScheduleService $scheduleService;
    private IDanceService $danceService;

    public function __construct(IScheduleService $scheduleService, IDanceService $danceService)
    {
        $this->scheduleService = $scheduleService;
        $this->danceService = $danceService;
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
            $this->applyDanceArtistImageMetadata($editorViewModel->performers),
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
            $venues = $this->mapVenueViewModels($postedVenues);
        }

        $performers = $editorData->performers;
        if (!empty($postedPerformers)) {
            $existingPerformers = strtolower($eventName) === 'dance' ? $editorData->performers : [];
            $performers = $this->mapPerformerViewModels($postedPerformers, $existingPerformers);
        }

        $sessions = $editorData->sessions;
        if (!empty($postedSessions)) {
            $sessions = $this->mapSessionViewModels($postedSessions);
        }

        return new ScheduleEditorViewModel($editorData->eventName, $venues, $performers, $sessions);
    }

    private function getDanceArtistImageRows(): array
    {
        $danceHome = $this->danceService->getDanceHomePage();
        $artistsSection = $danceHome->getSection('dance_artists');
        $artistImageRows = [];

        if ($artistsSection === null) {
            return $artistImageRows;
        }

        foreach ($artistsSection->getItemsByCategorie('artist') as $item) {
            if ($item instanceof SectionItem) {
                $artistImageRows[] = $item;
            }
        }

        return $artistImageRows;
    }

    private function applyDanceArtistImageMetadata(array $performers): array
    {
        $artistImageRows = $this->getDanceArtistImageRows();
        $result = [];

        foreach ($performers as $index => $performer) {
            if (!$performer instanceof ScheduleEditorPerformerRowViewModel) {
                continue;
            }

            $imageRow = $artistImageRows[$index] ?? null;
            $artistSectionItemId = $imageRow instanceof SectionItem ? $imageRow->id : 0;
            $artistImagePath = $imageRow instanceof SectionItem ? (string)($imageRow->image ?? '') : '';

            $result[] = new ScheduleEditorPerformerRowViewModel(
                $performer->id,
                $performer->name,
                $performer->type,
                $performer->description,
                $artistSectionItemId,
                $artistImagePath
            );
        }

        return $result;
    }

    private function mapVenueViewModels(array $rows): array
    {
        $venues = [];
        foreach ($rows as $row) {
            if (!$row instanceof ScheduleVenueRowRequest) {
                continue;
            }

            $venues[] = new ScheduleEditorVenueRowViewModel(
                $row->id(),
                $row->name(),
                $row->address(),
                $row->type()
            );
        }

        return $venues;
    }

    private function mapPerformerViewModels(array $rows, array $artistImageRows): array
    {
        $performers = [];
        foreach ($rows as $index => $row) {
            if (!$row instanceof SchedulePerformerRowRequest) {
                continue;
            }

            $imageRow = $artistImageRows[$index] ?? null;
            $artistSectionItemId = $imageRow instanceof ScheduleEditorPerformerRowViewModel ? $imageRow->artistSectionItemId : 0;
            $artistImagePath = $imageRow instanceof ScheduleEditorPerformerRowViewModel ? $imageRow->artistImagePath : '';

            $performers[] = new ScheduleEditorPerformerRowViewModel(
                $row->id(),
                $row->name(),
                $row->type(),
                $row->description(),
                $artistSectionItemId,
                $artistImagePath
            );
        }

        return $performers;
    }

    private function mapSessionViewModels(array $rows): array
    {
        $sessions = [];
        foreach ($rows as $row) {
            if (!$row instanceof ScheduleSessionRowRequest) {
                continue;
            }

            $sessions[] = new ScheduleEditorSessionRowViewModel(
                $row->id(),
                $row->date(),
                $row->startTime(),
                $row->venueId(),
                $row->label(),
                $row->price(),
                $row->availableSpots(),
                $row->amountSold(),
                array_values(array_map('intval', $row->performerIds()))
            );
        }

        return $sessions;
    }
}
