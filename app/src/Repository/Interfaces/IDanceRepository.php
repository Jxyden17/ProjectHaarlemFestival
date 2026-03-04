<?php

namespace App\Repository\Interfaces;

use App\Models\EventModel;

interface IDanceRepository
{
    public function findDanceEvent(): ?EventModel;
    public function countSessionsByEventId(int $eventId): int;
    public function countDistinctVenuesByEventId(int $eventId): int;
    public function getVenuesByEventId(int $eventId): array;
    public function getPerformersByEventId(int $eventId): array;
}
