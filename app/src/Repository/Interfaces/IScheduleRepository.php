<?php

namespace App\Repository\Interfaces;

use App\Models\Event\EventModel;

interface IScheduleRepository
{
    // Returns raw schedule join rows for one event so the mapper can rebuild the full event graph from SQL results.
    public function getScheduleRowsByEventName(string $name): array;
    // Returns raw schedule join rows filtered by performer id so callers can build performer-specific session lists.
    public function getScheduleRowsByEventNameAndPerformerId(string $name, int $performerId): array;
    // Finds one event by name so callers can branch on null when no such event exists.
    public function findEventByName(string $name): ?EventModel;
    // Returns session rows for one event so services can map event sessions without the larger join query.
    public function getSessionsByEventId(int $eventId): array;
    // Returns venue rows for one event so services and CMS editors can load allowed venue choices.
    public function getVenuesByEventId(int $eventId): array;
    // Returns performer rows for one event so services and CMS editors can load allowed performers.
    public function getPerformersByEventId(int $eventId): array;
    // Returns session-performer rows for one event so services can reconnect performer assignments to sessions.
    public function getSessionPerformersByEventId(int $eventId): array;
    // Saves all schedule-related rows for one event so CMS edits persist inside one repository transaction.
    public function saveEventScheduleData(int $eventId, array $venueRows, array $performerRows, array $sessionRows, array $sessionPerformerRows): void;
    // Returns all events so callers can build cross-event schedule views.
    public function getAllEvents(): array;
    public function findEventById(int $id): ?EventModel;
    public function editSchedule(int $id, int $eventId, int $venueId, string $date, string $startTime, int $availableSpots, ?string $label, ?float $price, ?int $language, array $performerIds = []): bool;
    public function createSchedule(int $eventId, int $venueId, string $date, string $startTime, int $availableSpots, ?string $label, ?float $price, ?int $language, array $performerIds = []): bool;
    public function deleteSchedule(int $id): bool;
    public function getSessionById(int $id): ?array;
}