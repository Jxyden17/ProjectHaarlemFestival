<?php

namespace App\Mapper;

use App\Models\Enums\Language;
use App\Models\Schedule\ScheduleData;
use App\Models\Event\SessionModel;
use App\Models\Event\SessionPerformerModel;
use App\Models\ViewModels\Shared\ScheduleDayFilterViewModel;
use App\Models\ViewModels\Shared\ScheduleGroupViewModel;
use App\Models\ViewModels\Shared\ScheduleRowViewModel;
use App\Models\ViewModels\Shared\ScheduleViewModel;

class ScheduleViewModelMapper
{
    // Maps typed schedule data into the shared schedule view model so public pages can render grouped sessions and filters.
    public function mapScheduleData(ScheduleData $scheduleData): ScheduleViewModel
    {
        return $this->mapScheduleViewModel(
            $scheduleData->sessions,
            $scheduleData->title,
            $scheduleData->eventName,
            $scheduleData->includeEventFilters
        );
    }

    // Maps a flat list of sessions into schedule row view models so callers can embed performer-specific schedule snippets.
    public function mapScheduleRows(array $sessions): array
    {
        $rows = [];

        foreach ($sessions as $session) {
            if (!$session instanceof SessionModel) {
                continue;
            }

            $rows[] = $this->mapScheduleRow($session, new \DateTime($session->date));
        }

        return $rows;
    }

    // Builds the grouped schedule view model so public pages get rows plus day, event, and language filters together.
    private function mapScheduleViewModel(
        array $sessions,
        string $title,
        string $eventName,
        bool $includeEventFilters = false
    ): ScheduleViewModel {
        $groups = [];
        $dayCounts = [];
        $eventCounts = [];
        $languageCounts = [];

        foreach ($sessions as $session) {
            if (!$session instanceof SessionModel) {
                continue;
            }

            $dt = new \DateTime($session->date);
            $dayLabel = $dt->format('l');
            $dayKey = strtolower($dayLabel);
            $groupKey = $session->date;
            $dayCounts[$dayLabel] = ($dayCounts[$dayLabel] ?? 0) + 1;

            if (!isset($groups[$groupKey]) || !$groups[$groupKey] instanceof ScheduleGroupViewModel) {
                $groups[$groupKey] = new ScheduleGroupViewModel(
                    $dt->format('l - F j, Y'),
                    $dayKey,
                    ''
                );
            }

            $groups[$groupKey]->rows[] = $this->mapScheduleRow($session, $dt);
            $this->addSessionLanguageCount($session, $languageCounts);

            if ($includeEventFilters) {
                $this->addSessionEventCount($session, $eventCounts);
            }
        }

        foreach ($groups as $group) {
            if (!$group instanceof ScheduleGroupViewModel) {
                continue;
            }

            $group->subtitle = count($group->rows) . ' events scheduled';
        }

        return new ScheduleViewModel(
            $title,
            $eventName,
            $this->buildDayFilters($dayCounts),
            $this->buildEventFilters($eventCounts),
            array_values($groups),
            $this->buildLanguageFilters($languageCounts)
        );
    }

    // Maps one session into a render-ready row so schedule cards get formatted dates, labels, and booking links.
    public function mapScheduleRow(SessionModel $session, \DateTimeInterface $dt): ScheduleRowViewModel
    {
        $language = $this->buildLanguageLabel($session->language);
        $eventName = $this->checkEvent($session->event?->name ?? 'Other');

        return new ScheduleRowViewModel(
            $session->id,
            $dt->format('M j, Y'),
            substr($session->startTime, 0, 5),
            $this->buildEventLabel($session),
            $session->venue !== null ? ($session->venue->venueName ?? 'Unknown venue') : 'Unknown venue',
            'EUR ' . number_format($session->price, 2, '.', ''),
            '/book/' . $session->id,
            $language['label'] ?? 'Unknown',
            $session->availableSpots,
            $session->amountSold,
            $eventName,
            trim((string) ($session->label ?? '')) !== '' ? (string) $session->label : 'N/A'
        );
    }

    // Builds the lineup label so sessions show performer names in a stable sorted order. Example: Mina + Echo -> 'Echo B2B Mina'.
    private function buildEventLabel(SessionModel $session): string
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

        return $lineup === [] ? 'Session' : implode(' B2B ', $lineup);
    }

    // Increments language counts so the schedule view can render language filters with totals.
    private function addSessionLanguageCount(SessionModel $session, array &$languageCounts): void
    {
        $language = $this->buildLanguageLabel($session->language);
        if ($language === null) {
            return;
        }

        $languageCounts[$language['key']] ??= [
            'label' => $language['label'],
            'count' => 0,
        ];
        $languageCounts[$language['key']]['count']++;
    }

    // Increments event counts so all-events schedules can render per-event filters with totals.
    private function addSessionEventCount(SessionModel $session, array &$eventCounts): void
    {
        $eventName = $this->checkEvent($session->event?->name ?? 'Other');
        $eventKey = $this->toFilterKey($eventName);

        $eventCounts[$eventKey] ??= [
            'label' => $eventName,
            'count' => 0,
        ];
        $eventCounts[$eventKey]['count']++;
    }

    // Builds day filter view models so schedules can be filtered by weekday in the UI.
    private function buildDayFilters(array $dayCounts): array
    {
        $dayFilters = [new ScheduleDayFilterViewModel('all', 'All Days', '', true)];

        foreach ($dayCounts as $day => $count) {
            $dayFilters[] = new ScheduleDayFilterViewModel(
                strtolower($day),
                $day,
                $count > 0 ? '(' . $count . ')' : '',
                false
            );
        }

        return $dayFilters;
    }

    // Builds event filter view models so all-events schedules can be filtered by event type in the UI.
    private function buildEventFilters(array $eventCounts): array
    {
        $eventFilters = [new ScheduleDayFilterViewModel('all', 'All Events', '', true)];

        foreach ($eventCounts as $event => $meta) {
            $eventFilters[] = new ScheduleDayFilterViewModel(
                strtolower((string) $event),
                (string) ($meta['label'] ?? $event),
                (int) ($meta['count'] ?? 0) > 0 ? '(' . (int) ($meta['count'] ?? 0) . ')' : '',
                false
            );
        }

        return $eventFilters;
    }

    // Builds language filter view models so schedules can be filtered by session language in the UI.
    private function buildLanguageFilters(array $languageCounts): array
    {
        $languageFilters = [new ScheduleDayFilterViewModel('all', 'All Languages', '', true)];

        foreach ($languageCounts as $language => $meta) {
            $languageFilters[] = new ScheduleDayFilterViewModel(
                strtolower((string) $language),
                (string) ($meta['label'] ?? $language),
                (int) ($meta['count'] ?? 0) > 0 ? '(' . (int) ($meta['count'] ?? 0) . ')' : '',
                false
            );
        }

        return $languageFilters;
    }

    // Resolves a filter key and label for one language enum so UI filters use stable slugs and readable text.
    private function buildLanguageLabel(?Language $language): ?array
    {
        if ($language === null) {
            return null;
        }

        $label = $language->label();

        return [
            'key' => $this->toFilterKey($label),
            'label' => $label,
        ];
    }

    // Normalizes event labels so different source names collapse into consistent public filter labels. Example: 'A stroll through history' -> 'Tour'.
    private function checkEvent(string $eventName): string
    {
        return match ($this->getEventType($eventName)) {
            'tour' => 'Tour',
            'stories' => 'Stories',
            'dance' => 'Dance',
            default => $eventName,
        };
    }

    // Detects the canonical event type so schedule filters can treat alias event names as one category.
    private function getEventType(string $eventName): string
    {
        $eventType = $this->toFilterKey($eventName);

        if ($eventType === 'astrollthroughhistory') {
            return 'tour';
        }

        if ($eventType === 'tellingstory') {
            return 'stories';
        }

        if ($eventType === 'dance') {
            return 'dance';
        }

        return 'other';
    }

    // Builds a compact lowercase filter key so UI filters stay stable across labels. Example: 'All Events' -> 'allevents'.
    private function toFilterKey(string $key): string
    {
        return strtolower(str_replace(' ', '', $key));
    }
}
