<?php

namespace App\Repository\Interfaces;

interface IYummyRepository
{
    public function findYummyEvent(): ?EventModel;

    public function getVenuesByEventId(int $eventId): array;
}