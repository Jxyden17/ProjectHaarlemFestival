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

        return $this->scheduleMapper->mapScheduleViewModel($event->sessions, $title);
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

        $event = $this->getEventRowsOrFail($eventName);
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
        $venues = $this->scheduleRepo->getVenuesByEventId((int)$event->id);
        $performers = $this->scheduleRepo->getPerformersByEventId((int)$event->id);
        $sessions = $this->scheduleRepo->getSessionsByEventId((int)$event->id);
        $sessionPerformers = $this->scheduleRepo->getSessionPerformersByEventId((int)$event->id);

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
}
