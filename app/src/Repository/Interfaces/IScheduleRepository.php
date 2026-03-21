<?php

namespace App\Repository\Interfaces;

use App\Models\Event\EventModel;

interface IScheduleRepository
{
    public function getScheduleRowsByEventName(string $name): array;
    public function getScheduleRowsByEventNameAndPerformerId(string $name, int $performerId): array;
    public function findEventByName(string $name): ?EventModel;
    public function getSessionsByEventId(int $eventId): array;
    public function getSessionById(int $id): ?\App\Models\Event\SessionModel;
    public function createSessionForTicketing(int $eventId, int $venueId, string $date, string $startTime, int $availableSpots): int;
    public function updateSessionForTicketing(int $id, int $eventId, string $date, string $startTime, int $availableSpots): bool;
    public function deleteSessionById(int $id, int $eventId): bool;
    public function getVenuesByEventId(int $eventId): array;
    public function getPerformersByEventId(int $eventId): array;
    public function getSessionPerformersByEventId(int $eventId): array;
    public function saveEventScheduleData(int $eventId, array $venueRows, array $performerRows, array $sessionRows, array $sessionPerformerRows): void;
    public function getAllEvents(): array;
}
