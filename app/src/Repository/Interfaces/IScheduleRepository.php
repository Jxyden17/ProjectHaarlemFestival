<?php

namespace App\Repository\Interfaces;

use App\Models\Event\EventModel;
use App\Models\Event\SessionModel;

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
    // Returns one session row by id so CMS schedule editing can preload an existing session into its editor view model.
    public function getSessionById(int $id): ?SessionModel;
    // Finds one event by id so CMS schedule editing can rebuild related venues and performers for that event.
    public function findEventById(int $id): ?EventModel;
    // Updates one schedule row so CMS forms can persist event, venue, timing, language, and pricing changes.
    public function editSchedule(int $id, int $eventId, int $venueId, string $date, string $startTime, int $availableSpots, ?string $label, ?float $price, ?int $language, array $performerIds = []): bool;
    // Creates one schedule row and optional performer links so CMS forms can add a new scheduled session.
    public function createSchedule(int $eventId, int $venueId, string $date, string $startTime, int $availableSpots, ?string $label, ?float $price, ?int $language, array $performerIds = []): bool;
    // Deletes one schedule row and its performer links so CMS schedule cleanup does not leave orphan assignments behind.
    public function deleteSchedule(int $id): bool;
}
