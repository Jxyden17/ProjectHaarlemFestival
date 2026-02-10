<?php

namespace App\Service;

use App\Repository\ScheduleRepository;
use App\Service\Interfaces\IScheduleService;

class ScheduleService implements IScheduleService
{
    private ScheduleRepository $scheduleRepo;

    public function __construct(ScheduleRepository $scheduleRepo)
    {
        $this->scheduleRepo = $scheduleRepo;
    }

    public function getDanceScheduleData(): array
    {
        $eventId = $this->scheduleRepo->findEventIdByName('Dance');
        $rows = $eventId ? $this->scheduleRepo->getSessionsByEventId($eventId) : [];

        return $this->buildScheduleData($rows);
    }

    private function buildScheduleData(array $rows): array
    {
        $groups = [];
        $dayCounts = [];

        foreach ($rows as $row) {
            $dateKey = $row['date'];
            $dt = new \DateTime($dateKey);

            $dayLabel = $dt->format('l');
            $dayKey = strtolower($dayLabel);
            $groupTitle = $dt->format('l - F j, Y');
            $dayCounts[$dayLabel] = ($dayCounts[$dayLabel] ?? 0) + 1;

            $groups[$dateKey]['title'] = $groupTitle;
            $groups[$dateKey]['day_key'] = $dayKey;
            $groups[$dateKey]['rows'][] = [
                'date' => $dt->format('M j, Y'),
                'time' => substr((string)$row['start_time'], 0, 5),
                'event' => $row['performer_lineup'] ?: 'Session',
                'location' => $row['venue_name'] ?? 'Unknown venue',
                'price' => $this->formatPrice((float)$row['price']),
                'book_url' => '/book?session_id=' . (int)$row['id'],
            ];
        }

        $formattedGroups = [];
        foreach ($groups as $group) {
            $formattedGroups[] = [
                'title' => $group['title'],
                'day_key' => $group['day_key'] ?? 'all',
                'subtitle' => count($group['rows']) . ' events scheduled',
                'rows' => $group['rows'],
            ];
        }

        $dayFilters = [['key' => 'all', 'label' => 'All Days', 'count' => count($rows), 'active' => true]];
        foreach ($dayCounts as $day => $count) {
            $dayFilters[] = [
                'key' => strtolower($day),
                'label' => $day,
                'count' => $count,
                'active' => false,
            ];
        }

        return [
            'title' => 'DANCE! Festival Schedule',
            'day_filters' => $dayFilters,
            'groups' => $formattedGroups,
        ];
    }

    private function formatPrice(float $price): string
    {
        return 'EUR ' . number_format($price, 2, '.', '');
    }
}
