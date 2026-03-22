<?php

namespace App\Mapper;

use App\Models\Enums\Language;
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
    private EventMapper $eventMapper;

    public function __construct(EventMapper $eventMapper)
    {
        $this->eventMapper = $eventMapper;
    }

    public function mapEventRow(array $row): EventModel
    {
        return $this->eventMapper->mapEventRow($row);
    }

    public function mapSessionRow(array $row): SessionModel
    {
        $languageId = isset($row['language_id']) ? (int) $row['language_id'] : null;

        return new SessionModel(
            (int) $row['id'],
            isset($row['event_id']) ? (int) $row['event_id'] : null,
            (int) $row['venue_id'],
            (string) $row['date'],
            (string) $row['start_time'],
            $languageId !== null ? Language::tryFrom($languageId) : null,
            isset($row['label']) ? (string) $row['label'] : null,
            (float) $row['price'],
            (int) $row['available_spots'],
            (int) $row['amount_sold']
        );
    }

    public function mapVenueModelRow(array $row): VenueModel
    {
        return $this->eventMapper->mapVenueRow($row);
    }

    public function mapPerformerModelRow(array $row): PerformerModel
    {
        return $this->eventMapper->mapPerformerRow($row);
    }

    public function mapSessionPerformerRow(array $row): SessionPerformerModel
    {
        return $this->eventMapper->mapSessionPerformerRow($row);
    }

    public function mapEventRowsToEvent(array $rows, string $eventName): EventModel
    {
        if ($rows === []) {
            throw new \RuntimeException($eventName . ' event not found.');
        }

        $event = $this->eventMapper->mapEventRowFromRows($rows[0]);
        $sessionById = [];
        $venueById = [];
        $performerById = [];
        $seenSessionPerformer = [];

        foreach ($rows as $row) {
            $sessionId = (int) ($row['session_id'] ?? 0);
            if ($sessionId > 0 && !isset($sessionById[$sessionId])) {
                $venueId = (int) ($row['venue_id_ref'] ?? 0);
                $venue = null;

                if ($venueId > 0) {
                    if (!isset($venueById[$venueId])) {
                        $venueById[$venueId] = $this->eventMapper->mapVenueRow([
                            'id' => $venueId,
                            'event_id' => $event->id,
                            'venue_name' => (string) ($row['venue_name'] ?? ''),
                            'address' => isset($row['address']) ? (string) $row['address'] : null,
                            'venue_type' => isset($row['venue_type']) ? (string) $row['venue_type'] : null,
                            'created_at' => isset($row['venue_created_at']) ? (string) $row['venue_created_at'] : null,
                        ]);
                    }

                    $venue = $venueById[$venueId];
                }

                $languageId = isset($row['language_id']) ? (int) $row['language_id'] : null;
                $session = new SessionModel(
                    $sessionId,
                    $event->id,
                    (int) ($row['venue_id'] ?? 0),
                    (string) ($row['date'] ?? ''),
                    (string) ($row['start_time'] ?? ''),
                    $languageId !== null ? Language::tryFrom($languageId) : null,
                    isset($row['label']) ? (string) $row['label'] : null,
                    (float) ($row['price'] ?? 0),
                    (int) ($row['available_spots'] ?? 0),
                    (int) ($row['amount_sold'] ?? 0),
                    $event,
                    $venue
                );

                $sessionById[$sessionId] = $session;
                $event->sessions[] = $session;

                if ($venue !== null) {
                    $venue->sessions[] = $session;
                }
            }

            $spSessionId = (int) ($row['sp_session_id'] ?? 0);
            $spPerformerId = (int) ($row['sp_performer_id'] ?? 0);
            if ($spSessionId <= 0 || $spPerformerId <= 0 || !isset($sessionById[$spSessionId])) {
                continue;
            }

            if (!isset($performerById[$spPerformerId])) {
                $performerById[$spPerformerId] = $this->eventMapper->mapPerformerRow([
                    'id' => $spPerformerId,
                    'event_id' => $event->id,
                    'performer_name' => (string) ($row['performer_name'] ?? ''),
                    'performer_type' => isset($row['performer_type']) ? (string) $row['performer_type'] : null,
                    'description' => isset($row['performer_description']) ? (string) $row['performer_description'] : null,
                    'created_at' => isset($row['performer_created_at']) ? (string) $row['performer_created_at'] : null,
                ]);
            }

            $key = $spSessionId . '-' . $spPerformerId;
            if (isset($seenSessionPerformer[$key])) {
                continue;
            }

            $seenSessionPerformer[$key] = true;
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

    public function mapEventRowsFromCollections(
        EventModel $event,
        array $venues,
        array $sessions,
        array $performers,
        array $sessionPerformers
    ): EventModel {
        $venueById = [];
        foreach ($venues as $venue) {
            if (!$venue instanceof VenueModel) {
                continue;
            }

            $venue->sessions = [];
            $venueById[$venue->id] = $venue;
        }

        $performerById = [];
        foreach ($performers as $performer) {
            if (!$performer instanceof PerformerModel) {
                continue;
            }

            $performer->sessionPerformers = [];
            $performerById[$performer->id] = $performer;
        }

        $sessionById = [];
        $event->sessions = [];
        foreach ($sessions as $session) {
            if (!$session instanceof SessionModel) {
                continue;
            }

            $session->event = $event;
            $session->venue = $venueById[$session->venueId] ?? null;
            $session->sessionPerformers = [];
            $sessionById[$session->id] = $session;
            $event->sessions[] = $session;

            if ($session->venue !== null) {
                $session->venue->sessions[] = $session;
            }
        }

        foreach ($sessionPerformers as $sessionPerformer) {
            if (!$sessionPerformer instanceof SessionPerformerModel) {
                continue;
            }

            $session = $sessionById[$sessionPerformer->sessionId] ?? null;
            $performer = $performerById[$sessionPerformer->performerId] ?? null;
            $sessionPerformer->session = $session;
            $sessionPerformer->performer = $performer;

            if ($session !== null) {
                $session->sessionPerformers[] = $sessionPerformer;
            }

            if ($performer !== null) {
                $performer->sessionPerformers[] = $sessionPerformer;
            }
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
                (string) ($venue->address ?? ''),
                (string) ($venue->venueType ?? '')
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
                (string) ($performer->performerType ?? ''),
                (string) ($performer->description ?? ''),
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

            $sessionPerformerMap[$sessionPerformer->sessionId] ??= [];
            $sessionPerformerMap[$sessionPerformer->sessionId][] = $sessionPerformer->performerId;
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
                (string) ($session->label ?? ''),
                number_format($session->price, 2, '.', ''),
                $session->availableSpots,
                $session->amountSold,
                array_values(array_map('intval', $sessionPerformerMap[$session->id] ?? []))
            );
        }

        return $rows;
    }

    public function mapScheduleViewModel(
        array $sessions,
        string $title,
        string $eventName,
        bool $includeEventFilters = false
    ): ScheduleViewModel {
        $groups = [];
        $dayCounts = [];
        $eventCounts = [];
        $languageCounts = [];

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
            $this->addSessionLanguageCount($session, $languageCounts);

            if ($includeEventFilters) {
                $this->addSessionEventCount($session, $eventCounts);
            }
        }

        foreach ($groups as $group) {
            if (!$group instanceof ScheduleGroupViewModel) {
                continue;
            }

            $group->subtitle = count($group->rows) . ' events scheduled';
        }

        return new ScheduleViewModel(
            $title,
            $eventName,
            $this->buildDayFilters($dayCounts),
            $this->buildEventFilters($eventCounts),
            array_values($groups),
            $this->buildLanguageFilters($languageCounts)
        );
    }

    public function mapScheduleRow(SessionModel $session, \DateTime $dt): ScheduleRowViewModel
    {
        $language = $this->buildLanguageLabel($session->language);
        $eventName = $this->checkEvent($session->event?->name ?? 'Other');

        return new ScheduleRowViewModel(
            $dt->format('M j, Y'),
            substr($session->startTime, 0, 5),
            $this->buildEventLabel($session),
            $session->venue !== null ? ($session->venue->venueName ?? 'Unknown venue') : 'Unknown venue',
            '€ ' . number_format($session->price, 2, '.', ''),
            '/book/' . $session->id,
            $language['label'] ?? 'Unknown',
            $session->availableSpots,
            $session->amountSold,
            $eventName,
            trim((string) ($session->label ?? '')) !== '' ? (string) $session->label : 'N/A'
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

        return $lineup === [] ? 'Session' : implode(' B2B ', $lineup);
    }

    private function addSessionLanguageCount(SessionModel $session, array &$languageCounts): void
    {
        $language = $this->buildLanguageLabel($session->language);
        if ($language === null) {
            return;
        }

        $languageCounts[$language['key']] ??= [
            'label' => $language['label'],
            'count' => 0,
        ];
        $languageCounts[$language['key']]['count']++;
    }

    private function addSessionEventCount(SessionModel $session, array &$eventCounts): void
    {
        $eventName = $this->checkEvent($session->event?->name ?? 'Other');
        $eventKey = $this->toFilterKey($eventName);

        $eventCounts[$eventKey] ??= [
            'label' => $eventName,
            'count' => 0,
        ];
        $eventCounts[$eventKey]['count']++;
    }

    private function buildDayFilters(array $dayCounts): array
    {
        $dayFilters = [new ScheduleDayFilterViewModel('all', 'All Days', '', true)];

        foreach ($dayCounts as $day => $count) {
            $dayFilters[] = new ScheduleDayFilterViewModel(
                strtolower($day),
                $day,
                $count > 0 ? '(' . $count . ')' : '',
                false
            );
        }

        return $dayFilters;
    }

    private function buildEventFilters(array $eventCounts): array
    {
        $eventFilters = [new ScheduleDayFilterViewModel('all', 'All Events', '', true)];

        foreach ($eventCounts as $event => $meta) {
            $eventFilters[] = new ScheduleDayFilterViewModel(
                strtolower((string) $event),
                (string) ($meta['label'] ?? $event),
                (int) ($meta['count'] ?? 0) > 0 ? '(' . (int) ($meta['count'] ?? 0) . ')' : '',
                false
            );
        }

        return $eventFilters;
    }

    private function buildLanguageFilters(array $languageCounts): array
    {
        $languageFilters = [new ScheduleDayFilterViewModel('all', 'All Languages', '', true)];

        foreach ($languageCounts as $language => $meta) {
            $languageFilters[] = new ScheduleDayFilterViewModel(
                strtolower((string) $language),
                (string) ($meta['label'] ?? $language),
                (int) ($meta['count'] ?? 0) > 0 ? '(' . (int) ($meta['count'] ?? 0) . ')' : '',
                false
            );
        }

        return $languageFilters;
    }

    private function buildLanguageLabel(?Language $language): ?array
    {
        if ($language === null) {
            return null;
        }

        $label = $language->label();

        return [
            'key' => $this->toFilterKey($label),
            'label' => $label,
        ];
    }

    private function checkEvent(string $eventName): string
    {
        return match ($this->getEventType($eventName)) {
            'tour' => 'Tour',
            'stories' => 'Stories',
            'dance' => 'Dance',
            default => $eventName,
        };
    }

    private function getEventType(string $eventName): string
    {
        $eventType = $this->toFilterKey($eventName);

        if ($eventType === 'astrollthroughhistory') {
            return 'tour';
        }

        if ($eventType === 'tellingstory') {
            return 'stories';
        }

        if ($eventType === 'dance') {
            return 'dance';
        }

        return 'other';
    }

    private function toFilterKey(string $key): string
    {
        return strtolower(str_replace(' ', '', $key));
    }
}
