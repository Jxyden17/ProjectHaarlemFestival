<?php

namespace App\Service\Cms;

use App\Mapper\CmsScheduleMapper;
use App\Models\Edit\Schedule\SchedulePerformerEditRow;
use App\Models\Edit\Schedule\ScheduleSaveInput;
use App\Models\Edit\Schedule\ScheduleSessionEditRow;
use App\Models\Edit\Schedule\ScheduleVenueEditRow;
use App\Models\Event\EventModel;
use App\Models\Event\PerformerModel;
use App\Models\Event\SessionModel;
use App\Models\Event\VenueModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorViewModel;
use App\Repository\Interfaces\IScheduleRepository;
use App\Service\Cms\Interfaces\ICmsScheduleService;
use App\Validator\CmsScheduleValidator;

class CmsScheduleService implements ICmsScheduleService
{
    private IScheduleRepository $scheduleRepo;
    private CmsScheduleMapper $cmsScheduleMapper;
    private CmsScheduleValidator $scheduleValidator;

    public function __construct(
        IScheduleRepository $scheduleRepo,
        CmsScheduleMapper $cmsScheduleMapper,
        CmsScheduleValidator $scheduleValidator
    )
    {
        $this->scheduleRepo = $scheduleRepo;
        $this->cmsScheduleMapper = $cmsScheduleMapper;
        $this->scheduleValidator = $scheduleValidator;
    }

    public function getScheduleEditorData(string $eventName): ScheduleEditorViewModel
    {
        $event = $this->findEventOrFail($eventName);
        $venues = $this->scheduleRepo->getVenuesByEventId((int)$event->id);
        $performers = $this->scheduleRepo->getPerformersByEventId((int)$event->id);
        $sessions = $this->scheduleRepo->getSessionsByEventId((int)$event->id);
        $sessionPerformers = $this->scheduleRepo->getSessionPerformersByEventId((int)$event->id);

        $sessionPerformerMap = $this->cmsScheduleMapper->buildSessionPerformerMap($sessionPerformers);

        return new ScheduleEditorViewModel(
            $event->name,
            $this->cmsScheduleMapper->mapVenueRows($venues),
            $this->cmsScheduleMapper->mapPerformerRows($performers),
            $this->cmsScheduleMapper->mapSessionRows($sessions, $sessionPerformerMap)
        );
    }

    public function saveScheduleData(string $eventName, ScheduleSaveInput $input): void
    {
        $event = $this->findEventOrFail($eventName);
        $venues = $this->scheduleRepo->getVenuesByEventId((int)$event->id);
        $performers = $this->scheduleRepo->getPerformersByEventId((int)$event->id);
        $sessions = $this->scheduleRepo->getSessionsByEventId((int)$event->id);

        $allowedVenueIds = $this->extractVenueIds($venues);
        $allowedPerformerIds = $this->extractPerformerIds($performers);
        $allowedSessionIds = $this->extractSessionIds($sessions);

        $venueRows = $this->normalizeVenueRows($input->venues());
        $performerRows = $this->normalizePerformerRows($input->performers());
        [$sessionRows, $sessionPerformerRows] = $this->normalizeSessionRows(
            $input->sessions(),
            $allowedVenueIds,
            $allowedPerformerIds,
            $allowedSessionIds
        );

        $this->scheduleValidator->validateSessionRowsNotEmpty($sessionRows);

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
            if (!$row instanceof ScheduleVenueEditRow) {
                continue;
            }

            $id = $row->id();
            $name = $row->name();
            $address = $row->address();
            $type = $row->type();

            $this->scheduleValidator->validateVenueRow($id, $name);

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
        $seenSlugs = [];

        foreach ($rows as $row) {
            if (!$row instanceof SchedulePerformerEditRow) {
                continue;
            }

            $id = $row->id();
            $name = $row->name();
            $type = $row->type();
            $description = $row->description();

            $slug = $this->normalizeSlug($name);
            $this->scheduleValidator->validatePerformerRow($id, $name, $slug, $seenSlugs);

            $seenSlugs[$slug] = true;

            $normalizedRows[] = [
                'id' => $id,
                'performer_name' => $name,
                'page_slug' => $slug,
                'performer_type' => $type !== '' ? $type : null,
                'description' => $description !== '' ? $description : null,
            ];
        }

        return $normalizedRows;
    }

    private function normalizeSlug(string $value): string
    {
        $slug = trim($value);
        $transliterated = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug);
        if ($transliterated !== false) {
            $slug = $transliterated;
        }

        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';

        return trim($slug, '-');
    }

    private function normalizeSessionRows(array $rows, array $allowedVenueIds, array $allowedPerformerIds, array $allowedSessionIds): array
    {
        $sessionRows = [];
        $sessionPerformerRows = [];
        $seenSessionPerformer = [];

        foreach ($rows as $row) {
            if (!$row instanceof ScheduleSessionEditRow) {
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

    private function normalizeSingleSessionRow(ScheduleSessionEditRow $row, array $allowedVenueIds, array $allowedSessionIds): array
    {
        $id = $row->id();
        $venueId = $row->venueId();
        $date = $row->date();
        $startTime = $row->startTime();
        $label = $row->label();
        $priceRaw = $row->price();
        $spots = $row->availableSpots();
        $amountSold = $row->amountSold();

        $this->scheduleValidator->validateSessionRow(
            $id,
            $venueId,
            $date,
            $startTime,
            $priceRaw,
            $spots,
            $amountSold,
            $allowedVenueIds,
            $allowedSessionIds
        );

        $price = (float)$priceRaw;

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

            $this->scheduleValidator->validatePerformerIdAllowed($performerId, $allowedPerformerIds);

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
