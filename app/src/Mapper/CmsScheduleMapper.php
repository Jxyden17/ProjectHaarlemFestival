<?php

namespace App\Mapper;

use App\Models\Edit\Schedule\SchedulePerformerEditRow;
use App\Models\Edit\Schedule\ScheduleSessionEditRow;
use App\Models\Edit\Schedule\ScheduleVenueEditRow;
use App\Models\Event\PerformerModel;
use App\Models\Event\SessionModel;
use App\Models\Event\SessionPerformerModel;
use App\Models\Event\VenueModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorPerformerRowViewModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorSessionRowViewModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorVenueRowViewModel;

class CmsScheduleMapper
{
    // Maps venue models into CMS editor rows so the schedule form can render existing venue data.
    public function mapVenueRows(array $venues): array
    {
        $rows = [];

        foreach ($venues as $venue) {
            if (!$venue instanceof VenueModel) {
                continue;
            }

            $rows[] = new ScheduleEditorVenueRowViewModel(
                $venue->id,
                $venue->venueName,
                (string) ($venue->address ?? ''),
                (string) ($venue->venueType ?? '')
            );
        }

        return $rows;
    }

    // Maps posted venue edit rows back into editor rows so validation errors can re-render submitted values.
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

    // Maps posted performer edit rows back into editor rows while preserving existing dance image metadata when available.
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

    // Maps performer models into CMS editor rows so the schedule form can render existing performer data.
    public function mapPerformerRows(array $performers): array
    {
        $rows = [];

        foreach ($performers as $performer) {
            if (!$performer instanceof PerformerModel) {
                continue;
            }

            $rows[] = new ScheduleEditorPerformerRowViewModel(
                $performer->id,
                $performer->performerName,
                (string) ($performer->performerType ?? ''),
                (string) ($performer->description ?? ''),
                0,
                ''
            );
        }

        return $rows;
    }

    // Builds a session-to-performer id map so CMS session rows can preload assigned performers. Example: session 12 -> [3, 7].
    public function buildSessionPerformerMap(array $sessionPerformers): array
    {
        $sessionPerformerMap = [];

        foreach ($sessionPerformers as $sessionPerformer) {
            if (!$sessionPerformer instanceof SessionPerformerModel) {
                continue;
            }

            $sessionPerformerMap[$sessionPerformer->sessionId] ??= [];
            $sessionPerformerMap[$sessionPerformer->sessionId][] = $sessionPerformer->performerId;
        }

        return $sessionPerformerMap;
    }

    // Maps posted session edit rows back into editor rows so validation errors can re-render submitted values.
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
                array_values(array_map('intval', $row->performerIds())),
                $row->languageId(),
            );
        }

        return $sessions;
    }

    // Maps session models into CMS editor rows so the schedule form can render existing session data.
    public function mapSessionRows(array $sessions, array $sessionPerformerMap): array
    {
        $rows = [];

        foreach ($sessions as $session) {
            if (!$session instanceof SessionModel) {
                continue;
            }

            $rows[] = new ScheduleEditorSessionRowViewModel(
                $session->id,
                $session->date,
                substr($session->startTime, 0, 5),
                $session->venueId,
                (string) ($session->label ?? ''),
                number_format($session->price, 2, '.', ''),
                $session->availableSpots,
                $session->amountSold,
                array_values(array_map('intval', $sessionPerformerMap[$session->id] ?? [])),
                $session->languageId ?? 1
            );
        }

        return $rows;
    }
}
