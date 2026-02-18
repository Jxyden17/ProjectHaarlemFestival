<?php

namespace App\Service;

use App\Models\ViewModels\Dance\DanceBannerStatsViewModel;
use App\Repository\DanceRepository;
use App\Service\Interfaces\IDanceService;

class DanceService implements IDanceService
{
    private DanceRepository $danceRepository;

    public function __construct(DanceRepository $danceRepository)
    {
        $this->danceRepository = $danceRepository;
    }

    public function getDanceBannerStats(): DanceBannerStatsViewModel
    {
        $event = $this->danceRepository->findDanceEvent();

        if ($event === null) {
            throw new \RuntimeException('Dance event not found.');
        }

        return new DanceBannerStatsViewModel(
            $this->danceRepository->countSessionsByEventId($event->id),
            $this->danceRepository->countDistinctVenuesByEventId($event->id)
        );
    }

    public function getDanceVenues(): array
    {
        $event = $this->danceRepository->findDanceEvent();

        if ($event === null) {
            return [];
        }

        return $this->danceRepository->getVenuesByEventId($event->id);
    }
}
