<?php

namespace App\Service\Cms;

use App\Models\Commands\Cms\Schedule\ScheduleSaveCommand;
use App\Models\Event\EventModel;
use App\Models\Event\PerformerModel;
use App\Models\Event\SessionModel;
use App\Models\Event\VenueModel;
use App\Models\Requests\Cms\Schedule\SchedulePerformerRowRequest;
use App\Models\Requests\Cms\Schedule\ScheduleSessionRowRequest;
use App\Models\Requests\Cms\Schedule\ScheduleVenueRowRequest;
use App\Repository\Interfaces\IScheduleRepository;
use App\Service\Cms\Interfaces\ICmsScheduleService;

class CmsScheduleService implements ICmsScheduleService
{
    private IScheduleRepository $scheduleRepo;

    public function __construct(IScheduleRepository $scheduleRepo)
    {
        $this->scheduleRepo = $scheduleRepo;
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
        [$sessionRows, $sessionPerformerRows] = $this->normalizeSessionRows(
            $command->sessions(),
            $allowedVenueIds,
            $allowedPerformerIds,
            $allowedSessionIds
        );

        if (count($sessionRows) === 0) {
            throw new \InvalidArgumentException('No schedule rows were provided.');
        }

        $this->scheduleRepo->saveEventScheduleData(
            (int)$event->id,
            $venueRows,
            $performerRows,
            $sessionRows,
            $sessionPerformerRows
        );
    }

    private function findEventOrFail(string $eventName): EventModel
    {
        $event = $this->scheduleRepo->findEventByName($eventName);
        if ($event === null) {
            throw new \RuntimeException($eventName . ' event not found.');
        }

        return $event;
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

    private function normalizeSessionRows(
        array $rows,
        array $allowedVenueIds,
        array $allowedPerformerIds,
        array $allowedSessionIds
    ): array {
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

    private function normalizeSingleSessionRow(
        ScheduleSessionRowRequest $row,
        array $allowedVenueIds,
        array $allowedSessionIds
    ): array {
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

    private function normalizeSessionPerformerRows(
        int $sessionId,
        array $performerIds,
        array $allowedPerformerIds,
        array &$seenSessionPerformer
    ): array {
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
}
