<?php

namespace App\Mapper;

use App\Models\Edit\Schedule\SchedulePerformerEditRow;
use App\Models\Edit\Schedule\ScheduleSessionEditRow;
use App\Models\Edit\Schedule\ScheduleVenueEditRow;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorPerformerRowViewModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorSessionRowViewModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorVenueRowViewModel;

class CmsScheduleMapper
{
    public function mapVenueViewModels(array $rows): array
    {
        $venues = [];

        foreach ($rows as $row) {
            if (!$row instanceof ScheduleVenueEditRow) {
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
            if (!$row instanceof SchedulePerformerEditRow) {
                continue;
            }

            $existingPerformer = $existingPerformers[$index] ?? null;
            $artistSectionItemId = $existingPerformer instanceof ScheduleEditorPerformerRowViewModel
                ? $existingPerformer->artistSectionItemId
                : 0;
            $artistImagePath = $existingPerformer instanceof ScheduleEditorPerformerRowViewModel
                ? $existingPerformer->artistImagePath
                : '';

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
            if (!$row instanceof ScheduleSessionEditRow) {
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
