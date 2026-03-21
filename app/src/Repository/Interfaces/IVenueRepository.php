<?php

namespace App\Repository\Interfaces;

use App\Models\Event\VenueModel;

interface IVenueRepository
{
    public function getByEventId(int $eventId): array;
    public function getById(int $id): ?VenueModel;
    public function create(VenueModel $venueModel): bool;
    public function update(VenueModel $venueModel): bool;
    public function delete(int $id): bool;
}
