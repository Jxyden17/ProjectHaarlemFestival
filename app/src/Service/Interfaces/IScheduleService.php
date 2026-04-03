<?php

namespace App\Service\Interfaces;

use App\Models\Event\EventModel;
use App\Models\Schedule\ScheduleData;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorViewModel;

interface IScheduleService
{
    // Returns one event schedule payload so public pages can render a typed schedule for a specific event. Example: event 'Dance' -> ScheduleData.
    public function getScheduleDataForEvent(string $eventName, string $title): ScheduleData;

    // Returns one combined schedule payload so pages can render sessions across all events in one grouped schedule.
    public function getScheduleDataForAllEvents(string $title): ScheduleData;

    // Returns event, schedule, performers, and venues together so callers avoid multiple schedule lookups. Example: event 'Dance' -> resources array.
    public function getEventResources(string $eventName, string $title): ?array;

    // Returns sessions that match a performer name so pages can show performer-specific schedule entries. Example: performer 'Mina' -> session list.
    public function getScheduleSessionsByPerformerName(string $eventName, string $performerName): array;

    // Returns sessions for one performer id so detail pages can show exact schedule entries without name matching. Example: performer id 7 -> session list.
    public function getScheduleSessionsByPerformerId(string $eventName, int $performerId): array;

    // Finds one event by name so other schedule flows can branch on null when the event does not exist.
    public function findEventByName(string $eventName): ?EventModel;
    public function getSessionById(int $sessionId): ?ScheduleEditorViewModel;
    public function editSchedule(int $id, int $eventId, int $venueId, string $date, string $startTime, int $availableSpots, ?string $label,  ?float $price, ?int $language): bool;
    public function createSchedule(    int $eventId, int $venueId, string $date, string $startTime, int $availableSpots, ?string $label, ?float $price, ?int $language, array $performerIds = []): bool;
    public function deleteSchedule(int $id): bool;
}