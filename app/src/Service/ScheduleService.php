<?php

namespace App\Service;

use App\Models\PerformerModel;
use App\Models\SessionModel;
use App\Models\SessionPerformerModel;
use App\Models\VenueModel;
use App\Models\EventModel;
use App\Repository\ScheduleRepository;
use App\Service\Interfaces\IScheduleService;
use App\Models\ViewModels\ScheduleViewModel;

class ScheduleService implements IScheduleService
{
    private ScheduleRepository $scheduleRepo;

    public function __construct(ScheduleRepository $scheduleRepo)
    {
        $this->scheduleRepo = $scheduleRepo;
    }

    public function getDanceScheduleData(): ScheduleViewModel
    {
        $event = $this->scheduleRepo->findEventByName('Dance');

        if ($event === null) {
            throw new \RuntimeException('Dance event not found.');
        }

        $venues = $this->scheduleRepo->getVenuesByEventId($event->id);
        $sessions = $this->scheduleRepo->getSessionsByEventId($event->id);
        $performers = $this->scheduleRepo->getPerformersByEventId($event->id);
        $sessionPerformers = $this->scheduleRepo->getSessionPerformersByEventId($event->id);

        $this->linkScheduleModels($event, $venues, $sessions, $performers, $sessionPerformers);
        $viewData = $this->buildScheduleViewData($event->sessions);

        return new ScheduleViewModel('DANCE! Festival Schedule', $viewData['dayFilters'], $viewData['groups']);
    }

    public function getDanceVenues(): array
    {
        $event = $this->scheduleRepo->findEventByName('Dance');

        if ($event === null) {
            return [];
        }

        return $this->scheduleRepo->getVenuesByEventId($event->id);
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

    private function buildScheduleViewData(array $sessions): array
    {
        $groups = [];
        $dayCounts = [];

        foreach ($sessions as $session) {
            if (!$session instanceof SessionModel) {
                continue;
            }

            $this->appendSessionToScheduleGroups($session, $groups, $dayCounts);
        }

        $dayFilters = $this->buildDayFilters($dayCounts, count($sessions));

        return [
            'dayFilters' => $dayFilters,
            'groups' => $groups,
        ];
    }

    private function appendSessionToScheduleGroups(SessionModel $session, array &$groups, array &$dayCounts): void
    {
        $dt = new \DateTime($session->date);
        $dayLabel = $dt->format('l');
        $dayKey = strtolower($dayLabel);
        $groupKey = $session->date;
        $dayCounts[$dayLabel] = ($dayCounts[$dayLabel] ?? 0) + 1;

        if (!isset($groups[$groupKey])) {
            $groups[$groupKey] = $this->createScheduleGroup($dt, $dayKey);
        }

        $groups[$groupKey]['rows'][] = $this->createScheduleRow($session, $dt);
    }

    private function createScheduleGroup(\DateTime $dt, string $dayKey): array
    {
        return [
            'title' => $dt->format('l - F j, Y'),
            'dayKey' => $dayKey,
            'rows' => [],
        ];
    }

    private function createScheduleRow(SessionModel $session, \DateTime $dt): array
    {
        $lineup = $this->buildPerformerLineup($session);

        return [
            'date' => $dt->format('M j, Y'),
            'time' => substr($session->startTime, 0, 5),
            'event' => !empty($lineup) ? implode(' B2B ', $lineup) : 'Session',
            'location' => $session->venue !== null ? $session->venue->venueName : 'Unknown venue',
            'price' => 'EUR ' . number_format($session->price, 2, '.', ''),
            'bookUrl' => '/book?session_id=' . $session->id,
        ];
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

    private function buildDayFilters(array $dayCounts, int $sessionCount): array
    {
        $dayFilters = [['key' => 'all', 'label' => 'All Days', 'count' => $sessionCount, 'active' => true]];
        foreach ($dayCounts as $day => $count) {
            $dayFilters[] = [
                'key' => strtolower($day),
                'label' => $day,
                'count' => $count,
                'active' => false,
            ];
        }

        return $dayFilters;
    }
}
