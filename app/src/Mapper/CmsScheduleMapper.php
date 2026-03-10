<?php

namespace App\Mapper;

use App\Models\Page\SectionItem;
use App\Models\Requests\Cms\Schedule\SchedulePerformerRowRequest;
use App\Models\Requests\Cms\Schedule\ScheduleSessionRowRequest;
use App\Models\Requests\Cms\Schedule\ScheduleVenueRowRequest;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorPerformerRowViewModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorSessionRowViewModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorVenueRowViewModel;
use App\Service\Interfaces\IDanceService;

class CmsScheduleMapper
{
    private IDanceService $danceService;

    public function __construct(IDanceService $danceService)
    {
        $this->danceService = $danceService;
    }

    public function applyDanceArtistImageMetadata(string $eventName, array $performers): array
    {
        if (strtolower($eventName) !== 'dance') {
            return $performers;
        }

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

    public function mapVenueViewModels(array $rows): array
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

    public function mapPerformerViewModels(array $rows, array $existingPerformers): array
    {
        $performers = [];
        foreach ($rows as $index => $row) {
            if (!$row instanceof SchedulePerformerRowRequest) {
                continue;
            }

            $existingPerformer = $existingPerformers[$index] ?? null;
            $artistSectionItemId = $existingPerformer instanceof ScheduleEditorPerformerRowViewModel ? $existingPerformer->artistSectionItemId : 0;
            $artistImagePath = $existingPerformer instanceof ScheduleEditorPerformerRowViewModel ? $existingPerformer->artistImagePath : '';

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

    public function mapSessionViewModels(array $rows): array
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
}
