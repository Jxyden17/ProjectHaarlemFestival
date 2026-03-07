<?php

namespace App\Service;

use App\Models\Enums\Language;
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
//get schedule voor een event
    public function getScheduleDataForEvent(string $eventName, string $title): ScheduleViewModel
    {
        $event = $this->findRequiredEventByName($eventName);

        $this->loadScheduleGraphForEvent($event);

        return $this->buildScheduleViewModel($event->sessions, $title, $event->name, includeEventFilters: false);
    }
// get all events in de schedule
    public function getScheduleDataForAllEvents(string $title): ScheduleViewModel
    {
        $events = $this->scheduleRepo->getAllEvents();
        $allSessions = [];

        if (empty($events)) 
        {
            throw new \RuntimeException('No events found.');
        }     

        foreach ($events as $event) 
            {
            $this->loadScheduleGraphForEvent($event);

            foreach ($event->sessions as $session) 
            {
                $allSessions[] = $session;
            }
        }
        return $this->buildScheduleViewModel($allSessions, $title, 'All Events', true);
    }
//schedule voor een event paken
    private function findRequiredEventByName(string $eventName): EventModel
    {
        $event = $this->scheduleRepo->findEventByName($eventName);

        if ($event === null) 
        {
            throw new \RuntimeException($eventName . ' event not found.');
        }
        return $event;
    }
//laden van schedule voor een event
    private function loadScheduleGraphForEvent(EventModel $event): void
    {
        $venues = $this->scheduleRepo->getVenuesByEventId($event->id);
        $sessions = $this->scheduleRepo->getSessionsByEventId($event->id);
        $performers = $this->scheduleRepo->getPerformersByEventId($event->id);
        $sessionPerformers = $this->scheduleRepo->getSessionPerformersByEventId($event->id);

        $this->linkScheduleModels($event, $venues, $sessions, $performers, $sessionPerformers);
    }
    private function linkScheduleModels(EventModel $event,array $venues,array $sessions,array $performers,array $sessionPerformers): void 
    {
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

    private function buildScheduleViewModel(array $sessions, string $title, string $eventName, bool $includeEventFilters): ScheduleViewModel
    {
        $groups = [];
        $dayCounts = [];
        $eventCounts = [];
        $languageCounts = [];

        foreach ($sessions as $session) {
            if (!$session instanceof SessionModel) {
                continue;
            }

            $this->appendSessionToScheduleGroups(
                $session,
                $groups,
                $dayCounts,
                $languageCounts,
                $eventCounts,
                $includeEventFilters
            );
        }

        $this->finalizeGroupSubtitles($groups);
        $dayFilters = $this->buildDayFilters($dayCounts);
        $eventFilters = $this->buildEventFilters($eventCounts);
        $languageFilters = $this->buildLanguageFilters($languageCounts);

        return new ScheduleViewModel(
            $title,
            $eventName,
            $dayFilters,
            $eventFilters,
            array_values($groups),
            $languageFilters
        );
    }

    private function addSessionLanguageCount(SessionModel $session, array &$languageCounts): void
    {
        $language = $this->buildLanguageLabel($session->language);
        if ($language === null) 
        {
            return;
        }
        $key = $language['key'];
        if (!isset($languageCounts[$key])) {
            $languageCounts[$key] = [
                'label' => $language['label'],
                'count' => 0,
            ];
        }

        $languageCounts[$key]['count']++;
    }

    private function addSessionEventCount(SessionModel $session, array &$eventCounts): void
    {
        $eventName = $this->checkEvent($session->event?->name ?? 'Other');
        $eventFilter = $this->toFilterKey($eventName);

        if (!isset($eventCounts[$eventFilter])) {
            $eventCounts[$eventFilter] = [
                'label' => $eventName,
                'count' => 0,
            ];
        }

        $eventCounts[$eventFilter]['count']++;
    }

    private function appendSessionToScheduleGroups(
        SessionModel $session,
        array &$groups,
        array &$dayCounts,
        array &$languageCounts,
        array &$eventCounts,
        bool $includeEventFilters
    ): void
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
        $this->addSessionLanguageCount($session, $languageCounts);

        if ($includeEventFilters) {
            $this->addSessionEventCount($session, $eventCounts);
        }
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
        $languageLabel = 'Unknown';
        $language = $this->buildLanguageLabel($session->language);
        if ($language !== null) {
            $languageLabel = $language['label'];
        }

        $ageLabel = trim((string)$session->label) !== '' ? (string)$session->label : 'N/A';

        $eventName = $this->checkEvent($session->event?->name ?? 'Other');



        $row = new ScheduleRowViewModel(
            $dt->format('M j, Y'),
            substr($session->startTime ?? '', 0, 5),
            $this->buildEventLabel($session),
            $session->venue !== null ? ($session->venue->venueName ?? 'Unknown venue') : 'Unknown venue',
            $this->formatPrice($session->price ?? 0.0),
            '/book?session_id=' . ($session->id ?? ''),
            $languageLabel,
            $session->availableSpots ?? 0,
            $session->amountSold ?? 0,
            $eventName,
            $ageLabel
        );

        return $row;
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
        foreach ($groups as $group) 
        {
            if (!$group instanceof ScheduleGroupViewModel) 
            {
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

    private function buildLanguageFilters(array $languageCounts): array
    {
        $languageFilters = [new ScheduleDayFilterViewModel('all', 'All Languages', '', true)];

        foreach ($languageCounts as $language => $meta) 
        {
            $languageFilters[] = new ScheduleDayFilterViewModel(
                strtolower((string)$language),
                (string)($meta['label'] ?? $language),
                $this->buildFilterCountLabel((int)($meta['count'] ?? 0)),
                false
            );
        }

        return $languageFilters;
    }

    private function buildEventFilters(array $eventCounts): array
    {
        $eventFilters = [new ScheduleDayFilterViewModel('all', 'All Events', '', true)];

        foreach ($eventCounts as $event => $meta) 
        {
            $eventFilters[] = new ScheduleDayFilterViewModel(
                strtolower((string)$event),
                (string)($meta['label'] ?? $event),
                $this->buildFilterCountLabel((int)($meta['count'] ?? 0)),
                false
            );
        }

        return $eventFilters;
    }

    private function buildLanguageLabel(?Language $language): ?array
    {
        if ($language === null)
        {
            return null;
        }
        $label = $language->label();

        return [
            'key' => $this->toFilterKey($label),
            'label' => $label,
        ];
    }
//nakijk event type voor juiste display name en filters
    private function getEventType(string $eventName): string
    {
        $eventType = $this->toFilterKey($eventName);

        if ($eventType === 'astrollthroughhistory') 
        {
            return 'tour';
        }
        if ($eventType === 'tellingstory') 
        {
            return 'stories';
        }
        return 'home';
    }


    private function checkEvent(string $eventName): string
    {
        $eventType = $this->getEventType($eventName);

        return match ($eventType) {
            'tour' => 'Tour',
            'stories' => 'Stories',
            'dance' => 'Dance',
            default => $eventName,
        };
    }
    private function buildFilterCountLabel(int $count): string
    {
        if ($count <= 0) {
            return '';
        }

        return '(' . $count . ')';
    }

    private function toFilterKey(string $key): string
    {
        return strtolower(str_replace(' ', '', $key));
    }
}