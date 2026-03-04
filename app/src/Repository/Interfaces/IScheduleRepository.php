<?php

namespace App\Repository\Interfaces;

use App\Models\Event\EventModel;

interface IScheduleRepository
{
    public function findEventByName(string $name): ?EventModel;
    public function getSessionsByEventId(int $eventId): array;
    public function getVenuesByEventId(int $eventId): array;
    public function getPerformersByEventId(int $eventId): array;
    public function getSessionPerformersByEventId(int $eventId): array;
    public function saveEventScheduleData(int $eventId, array $venueRows, array $performerRows, array $sessionRows, array $sessionPerformerRows): void;
}
