<?php

namespace App\Service;

use App\Models\Event\VenueModel;
use App\Repository\Interfaces\IVenueRepository;
use App\Service\Interfaces\IVenueService;

class VenueService implements IVenueService
{
    private IVenueRepository $venueRepository;

    public function __construct(IVenueRepository $venueRepository)
    {
        $this->venueRepository = $venueRepository;
    }

    public function getAllVenuesForEvent(int $eventId): array
    {
        return $this->venueRepository->getAllForEvent($eventId);
    }

    public function getVenueById(int $id): ?VenueModel
    {
        return $this->venueRepository->getById($id);
    }

    public function deleteVenueById(int $id): bool
    {
        return $this->venueRepository->delete($id);
    }

    public function updateVenue(VenueModel $venue): bool
    {
        return $this->venueRepository->update($venue);
    }

    public function addVenue(VenueModel $venue): bool
    {
        return $this->venueRepository->create($venue);
    }
}