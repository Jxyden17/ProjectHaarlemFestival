<?php

namespace App\Service;

use App\Mapper\ScheduleMapper;
use App\Models\Event\EventModel;
use App\Models\Event\SessionModel;
use App\Models\Event\SessionPerformerModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorViewModel;
use App\Models\ViewModels\Shared\ScheduleGroupViewModel;
use App\Models\ViewModels\Shared\ScheduleRowViewModel;
use App\Models\ViewModels\Shared\ScheduleViewModel;
use App\Repository\Interfaces\IScheduleRepository;
use App\Service\Interfaces\IScheduleService;

class ScheduleService implements IScheduleService
{
    private IScheduleRepository $scheduleRepo;
    private ScheduleMapper $scheduleMapper;

    public function __construct(IScheduleRepository $scheduleRepo, ScheduleMapper $scheduleMapper)
    {
        $this->scheduleRepo = $scheduleRepo;
        $this->scheduleMapper = $scheduleMapper;
    }

    public function getScheduleDataForEvent(string $eventName, string $title): ScheduleViewModel
    {
        $event = $this->getEventRowsOrFail($eventName);

        return $this->scheduleMapper->mapScheduleViewModel($event->sessions, $title, $event->name, false);
    }

    public function getScheduleDataForAllEvents(string $title): ScheduleViewModel
    {
        $events = $this->scheduleRepo->getAllEvents();
        $allSessions = [];

        if ($events === []) {
            throw new \RuntimeException('No events found.');
        }

        foreach ($events as $event) {
            if (!$event instanceof EventModel) {
                continue;
            }

            $eventFromRows = $this->mapEventRows($event);
            foreach ($eventFromRows->sessions as $session) {
                if ($session instanceof SessionModel) {
                    $allSessions[] = $session;
                }
            }
        }

        return $this->scheduleMapper->mapScheduleViewModel($allSessions, $title, 'All Events', true);
    }

    public function getScheduleRowsByPerformerName(string $eventName, string $performerName): array
    {
        $performer = trim((string) $performerName);
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

        $rows = $this->scheduleRepo->getScheduleRowsByEventNameAndPerformerId($eventName, $performerId);
        if ($rows === []) {
            return [];
        }

        $event = $this->scheduleMapper->mapEventRowsToEvent($rows, $eventName);
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

                $matchingRows[] = $this->scheduleMapper->mapScheduleRow($session, new \DateTime($session->date));
                break;
            }
        }

        return $matchingRows;
    }

    public function getScheduleEditorData(string $eventName): ScheduleEditorViewModel
    {
        $event = $this->findEventOrFail($eventName);
        $venues = $this->scheduleRepo->getVenuesByEventId((int) $event->id);
        $performers = $this->scheduleRepo->getPerformersByEventId((int) $event->id);
        $sessions = $this->scheduleRepo->getSessionsByEventId((int) $event->id);
        $sessionPerformers = $this->scheduleRepo->getSessionPerformersByEventId((int) $event->id);

        $sessionPerformerMap = $this->scheduleMapper->buildSessionPerformerMap($sessionPerformers);

        return new ScheduleEditorViewModel(
            $event->name,
            $this->scheduleMapper->mapVenueRows($venues),
            $this->scheduleMapper->mapPerformerRows($performers),
            $this->scheduleMapper->mapSessionRows($sessions, $sessionPerformerMap)
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

    private function getEventRowsOrFail(string $eventName): EventModel
    {
        $rows = $this->scheduleRepo->getScheduleRowsByEventName($eventName);

        return $this->scheduleMapper->mapEventRowsToEvent($rows, $eventName);
    }

    private function mapEventRows(EventModel $event): EventModel
    {
        $venues = $this->scheduleRepo->getVenuesByEventId((int) $event->id);
        $performers = $this->scheduleRepo->getPerformersByEventId((int) $event->id);
        $sessions = $this->scheduleRepo->getSessionsByEventId((int) $event->id);
        $sessionPerformers = $this->scheduleRepo->getSessionPerformersByEventId((int) $event->id);

        return $this->scheduleMapper->mapEventRowsFromCollections(
            $event,
            $venues,
            $sessions,
            $performers,
            $sessionPerformers
        );
    }

    public function getSessionById(int $id): ?ScheduleEditorViewModel
    {
        if ($id <= 0) {
        return null;
        }

        $session = $this->scheduleRepo->getSessionById($id);
        if ($session === null) {
            return null;
        }

        $eventId = (int)($session->eventId ?? 0);
        if ($eventId <= 0) {
            return null;
        }

        $event = $this->scheduleRepo->findEventById($eventId);
        if ($event === null) {
            return null;
        }

        $venues = $this->scheduleRepo->getVenuesByEventId($eventId);
        $performers = $this->scheduleRepo->getPerformersByEventId($eventId);
        $sessionPerformers = $this->scheduleRepo->getSessionPerformersByEventId($eventId);

        $sessionPerformerMap = $this->scheduleMapper->buildSessionPerformerMap($sessionPerformers);

        return new ScheduleEditorViewModel(
            $event->name,
            $this->scheduleMapper->mapVenueRows($venues),
            $this->scheduleMapper->mapPerformerRows($performers),
            $this->scheduleMapper->mapSessionRows([$session], $sessionPerformerMap)
        );
    }

    public function editSchedule(int $id, int $eventId, int $venueId, string $date, string $startTime, int $availableSpots, ?string $label, ?float $price, ?int $language): bool
    {
        return $this->scheduleRepo->editSchedule($id, $eventId, $venueId, $date, $startTime, $availableSpots, $label, $price, $language);
    }

    public function createSchedule(int $eventId, int $venueId, string $date, string $startTime, int $availableSpots, ?string $label, ?float $price, ?int $language, array $performerIds = []): bool
    {
        return $this->scheduleRepo->createSchedule($eventId, $venueId, $date, $startTime, $availableSpots, $label, $price, $language, $performerIds);
    }

    public function deleteSchedule(int $id): bool
    {
        return $this->scheduleRepo->deleteSchedule($id);
    }
}