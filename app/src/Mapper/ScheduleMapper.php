<?php

namespace App\Mapper;

use App\Models\Event\EventModel;
use App\Models\Event\PerformerModel;
use App\Models\Event\SessionModel;
use App\Models\Event\SessionPerformerModel;
use App\Models\Event\VenueModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorPerformerRowViewModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorSessionRowViewModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorVenueRowViewModel;
use App\Models\ViewModels\Shared\ScheduleDayFilterViewModel;
use App\Models\ViewModels\Shared\ScheduleGroupViewModel;
use App\Models\ViewModels\Shared\ScheduleRowViewModel;
use App\Models\ViewModels\Shared\ScheduleViewModel;

class ScheduleMapper
{
    public function mapEventRow(array $row): EventModel
    {
        return new EventModel(
            (int)$row['id'],
            (string)$row['name'],
            isset($row['description']) ? (string)$row['description'] : null
        );
    }

    public function mapSessionRow(array $row): SessionModel
    {
        return new SessionModel(
            (int)$row['id'],
            isset($row['event_id']) ? (int)$row['event_id'] : null,
            (int)$row['venue_id'],
            (string)$row['date'],
            (string)$row['start_time'],
            isset($row['label']) ? (string)$row['label'] : null,
            (float)$row['price'],
            (int)$row['available_spots'],
            (int)$row['amount_sold']
        );
    }

    public function mapVenueModelRow(array $row): VenueModel
    {
        return new VenueModel(
            (int)$row['id'],
            (int)$row['event_id'],
            (string)$row['venue_name'],
            isset($row['address']) ? (string)$row['address'] : null,
            isset($row['venue_type']) ? (string)$row['venue_type'] : null,
            isset($row['created_at']) ? (string)$row['created_at'] : null
        );
    }

    public function mapPerformerModelRow(array $row): PerformerModel
    {
        return new PerformerModel(
            (int)$row['id'],
            (int)$row['event_id'],
            (string)$row['performer_name'],
            isset($row['performer_type']) ? (string)$row['performer_type'] : null,
            isset($row['description']) ? (string)$row['description'] : null,
            isset($row['created_at']) ? (string)$row['created_at'] : null
        );
    }

    public function mapSessionPerformerRow(array $row): SessionPerformerModel
    {
        return new SessionPerformerModel(
            (int)$row['session_id'],
            (int)$row['performer_id']
        );
    }

    public function mapEventGraphRowsToEvent(array $rows, string $eventName): EventModel
    {
        if (empty($rows)) {
            throw new \RuntimeException($eventName . ' event not found.');
        }

        $firstRow = $rows[0];
        $event = new EventModel(
            (int)$firstRow['event_id'],
            (string)$firstRow['event_name'],
            isset($firstRow['event_description']) ? (string)$firstRow['event_description'] : null
        );

        $sessionById = [];
        $venueById = [];
        $performerById = [];
        $seenSessionPerformer = [];

        foreach ($rows as $row) {
            $sessionId = (int)($row['session_id'] ?? 0);
            if ($sessionId > 0 && !isset($sessionById[$sessionId])) {
                $venueId = (int)($row['venue_id_ref'] ?? 0);
                $venue = null;

                if ($venueId > 0) {
                    if (!isset($venueById[$venueId])) {
                        $venueById[$venueId] = new VenueModel(
                            $venueId,
                            $event->id,
                            (string)($row['venue_name'] ?? ''),
                            isset($row['address']) ? (string)$row['address'] : null,
                            isset($row['venue_type']) ? (string)$row['venue_type'] : null,
                            isset($row['venue_created_at']) ? (string)$row['venue_created_at'] : null
                        );
                    }

                    $venue = $venueById[$venueId];
                }

                $session = new SessionModel(
                    $sessionId,
                    $event->id,
                    (int)($row['venue_id'] ?? 0),
                    (string)($row['date'] ?? ''),
                    (string)($row['start_time'] ?? ''),
                    isset($row['label']) ? (string)$row['label'] : null,
                    (float)($row['price'] ?? 0),
                    (int)($row['available_spots'] ?? 0),
                    (int)($row['amount_sold'] ?? 0),
                    $event,
                    $venue
                );

                $sessionById[$sessionId] = $session;
                $event->sessions[] = $session;

                if ($venue !== null) {
                    $venue->sessions[] = $session;
                }
            }

            $spSessionId = (int)($row['sp_session_id'] ?? 0);
            $spPerformerId = (int)($row['sp_performer_id'] ?? 0);
            if ($spSessionId <= 0 || $spPerformerId <= 0 || !isset($sessionById[$spSessionId])) {
                continue;
            }

            if (!isset($performerById[$spPerformerId])) {
                $performerById[$spPerformerId] = new PerformerModel(
                    $spPerformerId,
                    $event->id,
                    (string)($row['performer_name'] ?? ''),
                    isset($row['performer_type']) ? (string)$row['performer_type'] : null,
                    isset($row['performer_description']) ? (string)$row['performer_description'] : null,
                    isset($row['performer_created_at']) ? (string)$row['performer_created_at'] : null
                );
            }

            $sessionPerformerKey = $spSessionId . '-' . $spPerformerId;
            if (isset($seenSessionPerformer[$sessionPerformerKey])) {
                continue;
            }

            $seenSessionPerformer[$sessionPerformerKey] = true;

            $sessionPerformer = new SessionPerformerModel(
                $spSessionId,
                $spPerformerId,
                $sessionById[$spSessionId],
                $performerById[$spPerformerId]
            );

            $sessionById[$spSessionId]->sessionPerformers[] = $sessionPerformer;
            $performerById[$spPerformerId]->sessionPerformers[] = $sessionPerformer;
        }

        return $event;
    }

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
                (string)($venue->address ?? ''),
                (string)($venue->venueType ?? '')
            );
        }

        return $rows;
    }

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
                (string)($performer->performerType ?? ''),
                (string)($performer->description ?? ''),
                0,
                ''
            );
        }

        return $rows;
    }

    public function buildSessionPerformerMap(array $sessionPerformers): array
    {
        $sessionPerformerMap = [];

        foreach ($sessionPerformers as $sessionPerformer) {
            if (!$sessionPerformer instanceof SessionPerformerModel) {
                continue;
            }

            $sessionId = $sessionPerformer->sessionId;
            if (!isset($sessionPerformerMap[$sessionId])) {
                $sessionPerformerMap[$sessionId] = [];
            }

            $sessionPerformerMap[$sessionId][] = $sessionPerformer->performerId;
        }

        return $sessionPerformerMap;
    }

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
                (string)($session->language ?? ''),
                number_format($session->price, 2, '.', ''),
                $session->availableSpots,
                $session->amountSold,
                array_values(array_map('intval', $sessionPerformerMap[$session->id] ?? []))
            );
        }

        return $rows;
    }

    public function mapScheduleViewModel(array $sessions, string $title): ScheduleViewModel
    {
        $groups = [];
        $dayCounts = [];

        foreach ($sessions as $session) {
            if (!$session instanceof SessionModel) {
                continue;
            }

            $dt = new \DateTime($session->date);
            $dayLabel = $dt->format('l');
            $dayKey = strtolower($dayLabel);
            $groupKey = $session->date;
            $dayCounts[$dayLabel] = ($dayCounts[$dayLabel] ?? 0) + 1;

            if (!isset($groups[$groupKey]) || !$groups[$groupKey] instanceof ScheduleGroupViewModel) {
                $groups[$groupKey] = new ScheduleGroupViewModel(
                    $dt->format('l - F j, Y'),
                    $dayKey,
                    ''
                );
            }

            $groups[$groupKey]->rows[] = $this->mapScheduleRow($session, $dt);
        }

        foreach ($groups as $group) {
            if (!$group instanceof ScheduleGroupViewModel) {
                continue;
            }

            $group->subtitle = count($group->rows) . ' events scheduled';
        }

        $dayFilters = [new ScheduleDayFilterViewModel('all', 'All Days', '', true)];

        foreach ($dayCounts as $day => $count) {
            $dayFilters[] = new ScheduleDayFilterViewModel(
                strtolower($day),
                $day,
                $count > 0 ? '(' . $count . ')' : '',
                false
            );
        }

        return new ScheduleViewModel($title, $dayFilters, array_values($groups));
    }

    public function mapScheduleRow(SessionModel $session, \DateTime $dt): ScheduleRowViewModel
    {
        return new ScheduleRowViewModel(
            $dt->format('M j, Y'),
            substr($session->startTime, 0, 5),
            $this->buildEventLabel($session),
            $session->venue !== null ? $session->venue->venueName : 'Unknown venue',
            '€ ' . number_format($session->price, 2, '.', ''),
            '/book?session_id=' . $session->id
        );
    }

    private function buildEventLabel(SessionModel $session): string
    {
        $lineup = [];

        foreach ($session->sessionPerformers as $sessionPerformer) {
            if (!$sessionPerformer instanceof SessionPerformerModel) {
                continue;
            }

            if ($sessionPerformer->performer !== null) {
                $lineup[] = $sessionPerformer->performer->performerName;
            }
        }

        sort($lineup);

        if (empty($lineup)) {
            return 'Session';
        }

        return implode(' B2B ', $lineup);
    }
}
