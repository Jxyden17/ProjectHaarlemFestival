<?php

namespace App\Service;

use App\Mapper\ScheduleMapper;
use App\Models\Event\EventModel;
use App\Models\Event\SessionModel;
use App\Models\Event\SessionPerformerModel;
use App\Models\Event\PerformerModel;
use App\Models\Schedule\ScheduleData;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorViewModel;
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

    public function getScheduleDataForEvent(string $eventName, string $title): ScheduleData
    {
        $event = $this->getEventRowsOrFail($eventName);

        return new ScheduleData($title, $event->name, $event->sessions, false);
    }

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

    public function findEventByName(string $eventName): ?EventModel
    {
        return $this->scheduleRepo->findEventByName($eventName);
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
}
