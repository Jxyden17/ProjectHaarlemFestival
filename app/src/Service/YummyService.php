<?php

namespace App\Service;

use App\Repository\YummyRepository;
use App\Service\Interfaces\IYummyService;

class YummyService implements IYummyService
{
    private YummyRepository $yummyRepository;

    public function __construct(YummyRepository $yummyRepository)
    {
        $this->yummyRepository = $yummyRepository;
    }

    public function getYummyVenues(): array
    {
        $event = $this->yummyRepository->findYummyEvent();

        if ($event === null) {
            return [];
        }

        return $this->yummyRepository->getVenuesByEventId($event->id);
    }
}
