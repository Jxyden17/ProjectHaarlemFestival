<?php

namespace App\Repository\Interfaces;

use App\Models\Event\EventModel;

interface IYummyRepository
{
    public function findYummyEvent(): ?EventModel;

    public function getVenuesByEventId(int $eventId): array;
}