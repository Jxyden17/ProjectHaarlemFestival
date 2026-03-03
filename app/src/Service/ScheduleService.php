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
use App\Models\ViewModels\Shared\ScheduleLanguageFilterViewModel;

class ScheduleService implements IScheduleService
{
    private ScheduleRepository $scheduleRepo;

    public function __construct(ScheduleRepository $scheduleRepo)
    {
        $this->scheduleRepo = $scheduleRepo;
    }

    public function getScheduleDataForEvent(string $eventName, string $title, bool $languageFilter = false): ScheduleViewModel
    {
        $event = $this->scheduleRepo->findEventByName($eventName);

        if ($event === null) {
            throw new \RuntimeException($eventName . ' event not found.');
        }

        $venues = $this->scheduleRepo->getVenuesByEventId($event->id);
        $sessions = $this->scheduleRepo->getSessionsByEventId($event->id);
        $performers = $this->scheduleRepo->getPerformersByEventId($event->id);
        $sessionPerformers = $this->scheduleRepo->getSessionPerformersByEventId($event->id);

        $this->linkScheduleModels($event, $venues, $sessions, $performers, $sessionPerformers);
        return $this->buildScheduleViewModel($event->sessions, $title, $languageFilter);
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
        $languageCounts = [];

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
        $language = $this->extractLanguageFromSession($session);

        // available and sold are stored on SessionModel as availableSpots and amountSold
        $available = isset($session->availableSpots) ? (int)$session->availableSpots : (int)($session->available_spots ?? 0);
        $sold = isset($session->amountSold) ? (int)$session->amountSold : (int)($session->amount_sold ?? 0);
        $total = $available + $sold;

        return new ScheduleRowViewModel(
            $dt->format('M j, Y'),
            substr($session->startTime ?? '', 0, 5),
            $this->buildEventLabel($session),
            $session->venue !== null ? ($session->venue->venueName ?? 'Unknown venue') : 'Unknown venue',
            $this->formatPrice($session->price ?? 0.0),
            '/book?session_id=' . ($session->id ?? ''),
            $language,
            $total,
            $available,
            $sold
        );
    }

    private function extractLanguageFromSession(SessionModel $session): string
    {
        if (isset($session->language) && $session->language !== null && $session->language !== '') {
            return (string)$session->language;
        }

        if (method_exists($session, 'getLanguage')) {
            $val = $session->getLanguage();
            if (!empty($val)) {
                return (string)$val;
            }
        }

        return 'Unknown';
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

        private function buildLanguageFilters(array $languageCounts): array
    {
        $languageFilters = [new ScheduleLanguageFilterViewModel('all', 'All Languages', '', true)];

        foreach ($languageCounts as $language => $count) {
            $languageFilters[] = new ScheduleLanguageFilterViewModel(
                strtolower($language),
                $language,
                $this->buildFilterCountLabel($count),
                false
            );
        }

        return $languageFilters;
    }

    private function buildFilterCountLabel(int $count): string
    {
        if ($count <= 0) {
            return '';
        }

        return '(' . $count . ')';
    }

    public function getEventFiltersForEventIds(array $eventIds): array
    {
        $counts = [];

        foreach ($eventIds as $id) {
            $event = $this->scheduleRepo->findEventById((int)$id);
            if ($event === null) {
                continue;
            }
            $sessions = $this->scheduleRepo->getSessionsByEventId($event->id);
            $counts[$event->id . '|' . $event->name] = ($counts[$event->id . '|' . $event->name] ?? 0) + count($sessions);
        }

        $filters = [new ScheduleDayFilterViewModel('all', 'All Events', '', true)];
        foreach ($counts as $key => $count) {
            [$id, $name] = explode('|', $key, 2);
            $slug = strtolower(str_replace(' ', '-', $name . '-' . $id));
            $filters[] = new ScheduleDayFilterViewModel($slug, $name, $this->buildFilterCountLabel($count), false);
        }

        return $filters;
    }
}
