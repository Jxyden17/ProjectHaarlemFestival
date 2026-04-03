<?php

namespace App\Service;

use App\Mapper\ScheduleMapper;
use App\Models\Event\EventModel;
use App\Models\Event\SessionModel;
use App\Models\Event\SessionPerformerModel;
use App\Models\Event\PerformerModel;
use App\Models\Schedule\ScheduleData;
use App\Repository\Interfaces\IScheduleRepository;
use App\Service\Interfaces\IScheduleService;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorViewModel;

class ScheduleService implements IScheduleService
{
    private IScheduleRepository $scheduleRepo;
    private ScheduleMapper $scheduleMapper;

    // Stores schedule dependencies so event loading and row mapping stay centralized in one public service.
    public function __construct(IScheduleRepository $scheduleRepo, ScheduleMapper $scheduleMapper)
    {
        $this->scheduleRepo = $scheduleRepo;
        $this->scheduleMapper = $scheduleMapper;
    }

    // Builds one event schedule payload so public pages receive grouped sessions with the requested title. Example: event 'Dance' -> ScheduleData.
    public function getScheduleDataForEvent(string $eventName, string $title): ScheduleData
    {
        $event = $this->getEventRowsOrFail($eventName);

        return new ScheduleData($title, $event->name, $event->sessions, false);
    }

    // Builds one combined schedule payload so pages can render sessions from every event in a single schedule view.
    public function getScheduleDataForAllEvents(string $title): ScheduleData
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

        return new ScheduleData($title, 'All Events', $allSessions, true);
    }

    // Loads the core resources for one event so callers can reuse the event, sessions, performers, and venues together.
    public function getEventResources(string $eventName, string $title): ?array
    {
        $event = $this->scheduleRepo->findEventByName($eventName);
        if ($event === null) {
            return null;
        }

        $performers = $this->scheduleRepo->getPerformersByEventId((int) $event->id);
        $venues = $this->scheduleRepo->getVenuesByEventId((int) $event->id);
        $fullEvent = $this->mapEventRows($event);

        return [
            'event' => $event,
            'schedule' => new ScheduleData($title, $event->name, $fullEvent->sessions, false),
            'performers' => $performers,
            'venues' => $venues,
        ];
    }

    // Finds sessions by performer name so search-like schedule views can match performers without knowing their ids.
    public function getScheduleSessionsByPerformerName(string $eventName, string $performerName): array
    {
        $performer = trim((string) $performerName);
        if ($performer === '') {
            return [];
        }

        $event = $this->getEventRowsOrFail($eventName);
        $matchingSessions = [];

        foreach ($event->sessions as $session) {
            if (!$session instanceof SessionModel) {
                continue;
            }

            foreach ($session->sessionPerformers as $sessionPerformer) {
                if (!$sessionPerformer instanceof SessionPerformerModel) {
                    continue;
                }

                $performerModel = $sessionPerformer->performer;
                if (!$performerModel instanceof PerformerModel) {
                    continue;
                }

                if (stripos($performerModel->performerName, $performer) === false) {
                    continue;
                }

                $matchingSessions[] = $session;
                break;
            }
        }

        return $matchingSessions;
    }

    // Finds sessions by performer id so detail pages can render exact schedule entries for one performer.
    public function getScheduleSessionsByPerformerId(string $eventName, int $performerId): array
    {
        if ($performerId <= 0) {
            return [];
        }

        $rows = $this->scheduleRepo->getScheduleRowsByEventNameAndPerformerId($eventName, $performerId);
        if ($rows === []) {
            return [];
        }

        $event = $this->scheduleMapper->mapEventRowsToEvent($rows, $eventName);
        $matchingSessions = [];

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

                $matchingSessions[] = $session;
                break;
            }
        }

        return $matchingSessions;
    }

    // Finds one event record by name so other services can branch on a missing event without throwing.
    public function findEventByName(string $eventName): ?EventModel
    {
        return $this->scheduleRepo->findEventByName($eventName);
    }

    // Loads and maps the full row set for one event so missing events fail immediately instead of producing partial schedule data.
    private function getEventRowsOrFail(string $eventName): EventModel
    {
        $rows = $this->scheduleRepo->getScheduleRowsByEventName($eventName);

        return $this->scheduleMapper->mapEventRowsToEvent($rows, $eventName);
    }

    // Rebuilds one event from repository collections so venues, sessions, and performers are linked into one graph.
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

        $eventId = (int)($session['event_id'] ?? 0);
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
        $sessionModel = $this->scheduleMapper->mapSessionRow($session);

        return new ScheduleEditorViewModel(
            $event->name,
            $this->scheduleMapper->mapVenueRows($venues),
            $this->scheduleMapper->mapPerformerRows($performers),
            $this->scheduleMapper->mapSessionRows([$sessionModel], $sessionPerformerMap)
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