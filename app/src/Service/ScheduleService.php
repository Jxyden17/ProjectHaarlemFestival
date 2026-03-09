<?php

namespace App\Service;

use App\Models\Commands\Cms\Schedule\ScheduleSaveCommand;
use App\Models\Event\PerformerModel;
use App\Models\Event\SessionModel;
use App\Models\Event\SessionPerformerModel;
use App\Models\Event\VenueModel;
use App\Models\Event\EventModel;
use App\Models\Requests\Cms\Schedule\SchedulePerformerRowRequest;
use App\Models\Requests\Cms\Schedule\ScheduleSessionRowRequest;
use App\Models\Requests\Cms\Schedule\ScheduleVenueRowRequest;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorPerformerRowViewModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorSessionRowViewModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorVenueRowViewModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorViewModel;
use App\Repository\Interfaces\IScheduleRepository;
use App\Service\Interfaces\IScheduleService;
use App\Models\ViewModels\Shared\ScheduleDayFilterViewModel;
use App\Models\ViewModels\Shared\ScheduleGroupViewModel;
use App\Models\ViewModels\Shared\ScheduleRowViewModel;
use App\Models\ViewModels\Shared\ScheduleViewModel;

class ScheduleService implements IScheduleService
{
    private IScheduleRepository $scheduleRepo;

    public function __construct(IScheduleRepository $scheduleRepo)
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

    public function getScheduleRowsByPerformerName(string $eventName, string $performerName): array
    {
        $performer = trim((string)$performerName);
        if ($performer === '') {
            return [];
        }

        $title = $eventName . ' Festival Schedule';
        $scheduleData = $this->getScheduleDataForEvent($eventName, $title);
        $matchingRows = [];

        foreach ($scheduleData->groups as $group) {
            if (!$group instanceof ScheduleGroupViewModel) {
                continue;
            }

            foreach ($group->rows as $row) {
                if (!$row instanceof ScheduleRowViewModel) {
                    continue;
                }

                if (stripos($row->event, $performer) === false) {
                    continue;
                }

                $matchingRows[] = $row;
            }
        }

        return $matchingRows;
    }

    public function getScheduleRowsByPerformerId(string $eventName, int $performerId): array
    {
        if ($performerId <= 0) {
            return [];
        }

        $event = $this->findEventOrFail($eventName);
        $venues = $this->scheduleRepo->getVenuesByEventId($event->id);
        $sessions = $this->scheduleRepo->getSessionsByEventId($event->id);
        $performers = $this->scheduleRepo->getPerformersByEventId($event->id);
        $sessionPerformers = $this->scheduleRepo->getSessionPerformersByEventId($event->id);

        $this->linkScheduleModels($event, $venues, $sessions, $performers, $sessionPerformers);

        $matchingRows = [];
        foreach ($event->sessions as $session) {
            if (!$session instanceof SessionModel) {
                continue;
            }

            foreach ($session->sessionPerformers as $sessionPerformer) {
                if (!$sessionPerformer instanceof SessionPerformerModel) {
                    continue;
                }

                if ($sessionPerformer->performerId !== $performerId) {
                    continue;
                }

                $matchingRows[] = $this->createScheduleRow($session, new \DateTime($session->date));
                break;
            }
        }

        return $matchingRows;
    }

    public function getScheduleEditorData(string $eventName): ScheduleEditorViewModel
    {
        $event = $this->findEventOrFail($eventName);

        $venues = $this->scheduleRepo->getVenuesByEventId((int)$event->id);
        $performers = $this->scheduleRepo->getPerformersByEventId((int)$event->id);
        $sessions = $this->scheduleRepo->getSessionsByEventId((int)$event->id);
        $sessionPerformers = $this->scheduleRepo->getSessionPerformersByEventId((int)$event->id);

        $sessionPerformerMap = $this->buildSessionPerformerMap($sessionPerformers);

        return new ScheduleEditorViewModel(
            $event->name,
            $this->mapVenueRows($venues),
            $this->mapPerformerRows($performers),
            $this->mapSessionRows($sessions, $sessionPerformerMap)
        );
    }

    public function saveScheduleData(string $eventName, ScheduleSaveCommand $command): void
    {
        $event = $this->findEventOrFail($eventName);

        $venues = $this->scheduleRepo->getVenuesByEventId((int)$event->id);
        $performers = $this->scheduleRepo->getPerformersByEventId((int)$event->id);
        $sessions = $this->scheduleRepo->getSessionsByEventId((int)$event->id);

        $allowedVenueIds = $this->extractVenueIds($venues);
        $allowedPerformerIds = $this->extractPerformerIds($performers);
        $allowedSessionIds = $this->extractSessionIds($sessions);

        $venueRows = $this->normalizeVenueRows($command->venues());
        $performerRows = $this->normalizePerformerRows($command->performers());
        [$sessionRows, $sessionPerformerRows] = $this->normalizeSessionRows($command->sessions(), $allowedVenueIds, $allowedPerformerIds, $allowedSessionIds);

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

    private function mapVenueRows(array $venues): array
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

    private function mapPerformerRows(array $performers): array
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

    private function buildSessionPerformerMap(array $sessionPerformers): array
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

    private function mapSessionRows(array $sessions, array $sessionPerformerMap): array
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

    private function extractVenueIds(array $venues): array
    {
        $ids = [];

        foreach ($venues as $venue) {
            if ($venue instanceof VenueModel) {
                $ids[] = $venue->id;
            }
        }

        return $ids;
    }

    private function extractPerformerIds(array $performers): array
    {
        $ids = [];

        foreach ($performers as $performer) {
            if ($performer instanceof PerformerModel) {
                $ids[] = $performer->id;
            }
        }

        return $ids;
    }

    private function extractSessionIds(array $sessions): array
    {
        $ids = [];

        foreach ($sessions as $session) {
            if ($session instanceof SessionModel) {
                $ids[] = $session->id;
            }
        }

        return $ids;
    }

    private function normalizeVenueRows(array $rows): array
    {
        $normalizedRows = [];

        foreach ($rows as $row) {
            if (!$row instanceof ScheduleVenueRowRequest) {
                continue;
            }

            $id = $row->id();
            $name = $row->name();
            $address = $row->address();
            $type = $row->type();

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
            if (!$row instanceof SchedulePerformerRowRequest) {
                continue;
            }

            $id = $row->id();
            $name = $row->name();
            $type = $row->type();
            $description = $row->description();

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
            if (!$row instanceof ScheduleSessionRowRequest) {
                continue;
            }

            $normalizedSessionRow = $this->normalizeSingleSessionRow($row, $allowedVenueIds, $allowedSessionIds);
            $sessionRows[] = $normalizedSessionRow;

            $normalizedSessionPerformerRows = $this->normalizeSessionPerformerRows(
                $normalizedSessionRow['id'],
                $row->performerIds(),
                $allowedPerformerIds,
                $seenSessionPerformer
            );

            foreach ($normalizedSessionPerformerRows as $sessionPerformerRow) {
                $sessionPerformerRows[] = $sessionPerformerRow;
            }
        }

        return [$sessionRows, $sessionPerformerRows];
    }

    private function normalizeSingleSessionRow(ScheduleSessionRowRequest $row, array $allowedVenueIds, array $allowedSessionIds): array
    {
        $id = $row->id();
        $venueId = $row->venueId();
        $date = $row->date();
        $startTime = $row->startTime();
        $label = $row->label();
        $priceRaw = $row->price();
        $spots = $row->availableSpots();
        $amountSold = $row->amountSold();

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

        return [
            'id' => $id,
            'venue_id' => $venueId,
            'date' => $date,
            'start_time' => $startTime . ':00',
            'label' => $label !== '' ? $label : null,
            'price' => $price,
            'available_spots' => $spots,
        ];
    }

    private function normalizeSessionPerformerRows(int $sessionId, array $performerIds, array $allowedPerformerIds, array &$seenSessionPerformer): array
    {
        $normalizedRows = [];

        foreach ($performerIds as $performerIdRaw) {
            $performerId = (int)$performerIdRaw;
            if ($performerId <= 0) {
                continue;
            }

            if (!in_array($performerId, $allowedPerformerIds, true)) {
                throw new \InvalidArgumentException('One or more selected performers are invalid for this event.');
            }

            $key = $sessionId . '-' . $performerId;
            if (isset($seenSessionPerformer[$key])) {
                continue;
            }

            $seenSessionPerformer[$key] = true;
            $normalizedRows[] = [
                'session_id' => $sessionId,
                'performer_id' => $performerId,
            ];
        }

        return $normalizedRows;
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
