<?php

namespace App\Service;

use App\Models\PerformerModel;
use App\Models\SessionModel;
use App\Models\SessionPerformerModel;
use App\Models\VenueModel;
use App\Models\EventModel;
use App\Repository\ScheduleRepository;
use App\Service\Interfaces\IScheduleService;
use App\Models\ViewModels\Shared\ScheduleDayFilterViewModel;
use App\Models\ViewModels\Shared\ScheduleGroupViewModel;
use App\Models\ViewModels\Shared\ScheduleRowViewModel;
use App\Models\ViewModels\Shared\ScheduleViewModel;

class ScheduleService implements IScheduleService
{
    private ScheduleRepository $scheduleRepo;

    public function __construct(ScheduleRepository $scheduleRepo)
    {
        $this->scheduleRepo = $scheduleRepo;
    }

    public function getScheduleDataForEvent(string $eventName, string $title): ScheduleViewModel
    {
        $event = $this->findEventOrFail($eventName);

        $venues = $this->scheduleRepo->getVenuesByEventId($event->id);
        $sessions = $this->scheduleRepo->getSessionsByEventId($event->id);
        $performers = $this->scheduleRepo->getPerformersByEventId($event->id);
        $sessionPerformers = $this->scheduleRepo->getSessionPerformersByEventId($event->id);

        $this->linkScheduleModels($event, $venues, $sessions, $performers, $sessionPerformers);
        return $this->buildScheduleViewModel($event->sessions, $title);
    }

    public function getScheduleEditorData(string $eventName): array
    {
        $event = $this->findEventOrFail($eventName);

        $venues = $this->scheduleRepo->getVenuesByEventId((int)$event->id);
        $performers = $this->scheduleRepo->getPerformersByEventId((int)$event->id);
        $sessions = $this->scheduleRepo->getSessionsByEventId((int)$event->id);
        $sessionPerformers = $this->scheduleRepo->getSessionPerformersByEventId((int)$event->id);

        $venueRows = [];
        foreach ($venues as $venue) {
            if ($venue instanceof VenueModel) {
                $venueRows[] = [
                    'id' => $venue->id,
                    'name' => $venue->venueName,
                    'address' => (string)($venue->address ?? ''),
                    'type' => (string)($venue->venueType ?? ''),
                ];
            }
        }

        $performerRows = [];
        foreach ($performers as $performer) {
            if ($performer instanceof PerformerModel) {
                $performerRows[] = [
                    'id' => $performer->id,
                    'name' => $performer->performerName,
                    'type' => (string)($performer->performerType ?? ''),
                    'description' => (string)($performer->description ?? ''),
                ];
            }
        }

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

        $sessionRows = [];
        foreach ($sessions as $session) {
            if (!$session instanceof SessionModel) {
                continue;
            }

            $sessionRows[] = [
                'id' => $session->id,
                'date' => $session->date,
                'start_time' => substr($session->startTime, 0, 5),
                'venue_id' => $session->venueId,
                'label' => (string)($session->language ?? ''),
                'price' => number_format($session->price, 2, '.', ''),
                'available_spots' => $session->availableSpots,
                'amount_sold' => $session->amountSold,
                'performer_ids' => $sessionPerformerMap[$session->id] ?? [],
            ];
        }

        return [
            'event_name' => $event->name,
            'venues' => $venueRows,
            'performers' => $performerRows,
            'sessions' => $sessionRows,
        ];
    }

    public function saveScheduleData(string $eventName, array $input): void
    {
        $event = $this->findEventOrFail($eventName);

        $venues = $this->scheduleRepo->getVenuesByEventId((int)$event->id);
        $performers = $this->scheduleRepo->getPerformersByEventId((int)$event->id);
        $sessions = $this->scheduleRepo->getSessionsByEventId((int)$event->id);

        $allowedVenueIds = [];
        foreach ($venues as $venue) {
            if ($venue instanceof VenueModel) {
                $allowedVenueIds[] = $venue->id;
            }
        }

        $allowedPerformerIds = [];
        foreach ($performers as $performer) {
            if ($performer instanceof PerformerModel) {
                $allowedPerformerIds[] = $performer->id;
            }
        }

        $allowedSessionIds = [];
        foreach ($sessions as $session) {
            if ($session instanceof SessionModel) {
                $allowedSessionIds[] = $session->id;
            }
        }

        $venueRowsInput = is_array($input['venues'] ?? null) ? $input['venues'] : [];
        $performerRowsInput = is_array($input['performers'] ?? null) ? $input['performers'] : [];
        $sessionRowsInput = is_array($input['sessions'] ?? null) ? $input['sessions'] : [];

        $venueRows = $this->normalizeVenueRows($venueRowsInput);
        $performerRows = $this->normalizePerformerRows($performerRowsInput);
        [$sessionRows, $sessionPerformerRows] = $this->normalizeSessionRows($sessionRowsInput, $allowedVenueIds, $allowedPerformerIds, $allowedSessionIds);

        if (count($sessionRows) === 0) {
            throw new \InvalidArgumentException('No schedule rows were provided.');
        }

        $this->scheduleRepo->saveEventScheduleData((int)$event->id, $venueRows, $performerRows, $sessionRows, $sessionPerformerRows);
    }

    private function findEventOrFail(string $eventName): EventModel
    {
        $event = $this->scheduleRepo->findEventByName($eventName);
        if ($event === null) {
            throw new \RuntimeException($eventName . ' event not found.');
        }

        return $event;
    }

    private function normalizeVenueRows(array $rows): array
    {
        $normalizedRows = [];

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $id = (int)($row['id'] ?? 0);
            $name = trim((string)($row['name'] ?? ''));
            $address = trim((string)($row['address'] ?? ''));
            $type = trim((string)($row['type'] ?? ''));

            if ($id <= 0 || $name === '') {
                throw new \InvalidArgumentException('Each venue row requires id and venue name.');
            }

            $normalizedRows[] = [
                'id' => $id,
                'venue_name' => $name,
                'address' => $address !== '' ? $address : null,
                'venue_type' => $type !== '' ? $type : null,
            ];
        }

        return $normalizedRows;
    }

    private function normalizePerformerRows(array $rows): array
    {
        $normalizedRows = [];

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $id = (int)($row['id'] ?? 0);
            $name = trim((string)($row['name'] ?? ''));
            $type = trim((string)($row['type'] ?? ''));
            $description = trim((string)($row['description'] ?? ''));

            if ($id <= 0 || $name === '') {
                throw new \InvalidArgumentException('Each performer row requires id and name.');
            }

            $normalizedRows[] = [
                'id' => $id,
                'performer_name' => $name,
                'performer_type' => $type !== '' ? $type : null,
                'description' => $description !== '' ? $description : null,
            ];
        }

        return $normalizedRows;
    }

    private function normalizeSessionRows(array $rows, array $allowedVenueIds, array $allowedPerformerIds, array $allowedSessionIds): array
    {
        $sessionRows = [];
        $sessionPerformerRows = [];
        $seenSessionPerformer = [];

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $id = (int)($row['id'] ?? 0);
            $venueId = (int)($row['venue_id'] ?? 0);
            $date = trim((string)($row['date'] ?? ''));
            $startTime = trim((string)($row['start_time'] ?? ''));
            $label = trim((string)($row['label'] ?? ''));
            $priceRaw = trim((string)($row['price'] ?? ''));
            $spots = (int)($row['available_spots'] ?? 0);
            $amountSold = (int)($row['amount_sold'] ?? 0);

            if ($id <= 0 || $venueId <= 0 || $date === '' || $startTime === '' || $priceRaw === '') {
                throw new \InvalidArgumentException('All schedule rows must include id, venue, date, time, and price.');
            }

            if (!in_array($id, $allowedSessionIds, true)) {
                throw new \InvalidArgumentException('One or more session ids are invalid for this event.');
            }

            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                throw new \InvalidArgumentException('Date must be in YYYY-MM-DD format.');
            }

            if (!preg_match('/^\d{2}:\d{2}$/', $startTime)) {
                throw new \InvalidArgumentException('Start time must be in HH:MM format.');
            }

            if (!in_array($venueId, $allowedVenueIds, true)) {
                throw new \InvalidArgumentException('Selected venue is not valid for this event.');
            }

            if (!is_numeric($priceRaw)) {
                throw new \InvalidArgumentException('Price must be numeric.');
            }

            $price = (float)$priceRaw;
            if ($price < 0) {
                throw new \InvalidArgumentException('Price cannot be negative.');
            }

            if ($spots < $amountSold) {
                throw new \InvalidArgumentException('Available spots cannot be lower than amount sold.');
            }

            $sessionRows[] = [
                'id' => $id,
                'venue_id' => $venueId,
                'date' => $date,
                'start_time' => $startTime . ':00',
                'label' => $label !== '' ? $label : null,
                'price' => $price,
                'available_spots' => $spots,
            ];

            $performerIds = is_array($row['performer_ids'] ?? null) ? $row['performer_ids'] : [];
            foreach ($performerIds as $performerIdRaw) {
                $performerId = (int)$performerIdRaw;
                if ($performerId <= 0) {
                    continue;
                }

                if (!in_array($performerId, $allowedPerformerIds, true)) {
                    throw new \InvalidArgumentException('One or more selected performers are invalid for this event.');
                }

                $key = $id . '-' . $performerId;
                if (isset($seenSessionPerformer[$key])) {
                    continue;
                }

                $seenSessionPerformer[$key] = true;
                $sessionPerformerRows[] = [
                    'session_id' => $id,
                    'performer_id' => $performerId,
                ];
            }
        }

        return [$sessionRows, $sessionPerformerRows];
    }

    private function linkScheduleModels(EventModel $event,array $venues,array $sessions,array $performers,array $sessionPerformers): void {
        $venueById = $this->indexVenuesById($venues);
        $performerById = $this->indexPerformersById($performers);
        $sessionById = $this->linkSessionsToEventAndVenue($event, $sessions, $venueById);
        $this->linkSessionPerformers($sessionPerformers, $sessionById, $performerById);
    }

    private function indexVenuesById(array $venues): array
    {
        $venueById = [];
        foreach ($venues as $venue) {
            if (!$venue instanceof VenueModel) {
                continue;
            }

            $venue->sessions = [];
            $venueById[$venue->id] = $venue;
        }

        return $venueById;
    }

    private function indexPerformersById(array $performers): array
    {
        $performerById = [];
        foreach ($performers as $performer) {
            if (!$performer instanceof PerformerModel) {
                continue;
            }

            $performer->sessionPerformers = [];
            $performerById[$performer->id] = $performer;
        }

        return $performerById;
    }

    private function linkSessionsToEventAndVenue(EventModel $event, array $sessions, array $venueById): array
    {
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

        return $sessionById;
    }

    private function linkSessionPerformers(array $sessionPerformers, array $sessionById, array $performerById): void
    {
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
    }

    private function buildScheduleViewModel(array $sessions, string $title): ScheduleViewModel
    {
        $groups = [];
        $dayCounts = [];

        foreach ($sessions as $session) {
            if (!$session instanceof SessionModel) {
                continue;
            }

            $this->appendSessionToScheduleGroups($session, $groups, $dayCounts);
        }

        $this->finalizeGroupSubtitles($groups);
        $dayFilters = $this->buildDayFilters($dayCounts);

        return new ScheduleViewModel($title, $dayFilters, array_values($groups));
    }

    private function appendSessionToScheduleGroups(SessionModel $session, array &$groups, array &$dayCounts): void
    {
        $dt = new \DateTime($session->date);
        $dayLabel = $dt->format('l');
        $dayKey = strtolower($dayLabel);
        $groupKey = $session->date;
        $dayCounts[$dayLabel] = ($dayCounts[$dayLabel] ?? 0) + 1;

        if (!isset($groups[$groupKey]) || !$groups[$groupKey] instanceof ScheduleGroupViewModel) {
            $groups[$groupKey] = $this->createScheduleGroup($dt, $dayKey);
        }

        $groups[$groupKey]->rows[] = $this->createScheduleRow($session, $dt);
    }

    private function createScheduleGroup(\DateTime $dt, string $dayKey): ScheduleGroupViewModel
    {
        return new ScheduleGroupViewModel(
            $dt->format('l - F j, Y'),
            $dayKey,
            ''
        );
    }

    private function createScheduleRow(SessionModel $session, \DateTime $dt): ScheduleRowViewModel
    {
        return new ScheduleRowViewModel(
            $dt->format('M j, Y'),
            substr($session->startTime, 0, 5),
            $this->buildEventLabel($session),
            $session->venue !== null ? $session->venue->venueName : 'Unknown venue',
            $this->formatPrice($session->price),
            '/book?session_id=' . $session->id
        );
    }

    private function buildEventLabel(SessionModel $session): string
    {
        $lineup = $this->buildPerformerLineup($session);

        if (empty($lineup)) {
            return 'Session';
        }

        return implode(' B2B ', $lineup);
    }

    private function buildPerformerLineup(SessionModel $session): array
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

        return $lineup;
    }

    private function formatPrice(float $price): string
    {
        return '€ ' . number_format($price, 2, '.', '');
    }

    private function finalizeGroupSubtitles(array $groups): void
    {
        foreach ($groups as $group) {
            if (!$group instanceof ScheduleGroupViewModel) {
                continue;
            }

            $group->subtitle = count($group->rows) . ' events scheduled';
        }
    }

    private function buildDayFilters(array $dayCounts): array
    {
        $dayFilters = [new ScheduleDayFilterViewModel('all', 'All Days', '', true)];

        foreach ($dayCounts as $day => $count) {
            $dayFilters[] = new ScheduleDayFilterViewModel(
                strtolower($day),
                $day,
                $this->buildFilterCountLabel($count),
                false
            );
        }

        return $dayFilters;
    }

    private function buildFilterCountLabel(int $count): string
    {
        if ($count <= 0) {
            return '';
        }

        return '(' . $count . ')';
    }
}
