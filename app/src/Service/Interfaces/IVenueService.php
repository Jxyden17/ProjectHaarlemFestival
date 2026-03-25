<?php

namespace App\Service\Interfaces;   

use App\Models\Event\VenueModel;

interface IVenueService
{
    public function getAllVenuesForEvent(int $eventId): array;
    public function getVenueById(int $id): ?VenueModel;
    public function deleteVenueById(int $id): bool;
    public function updateVenue(VenueModel $venue): bool;
    public function addVenue(VenueModel $venue): bool;
}