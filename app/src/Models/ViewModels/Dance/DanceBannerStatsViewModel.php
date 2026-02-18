<?php

namespace App\Models\ViewModels\Dance;

class DanceBannerStatsViewModel
{
    public int $totalEvents;
    public int $totalLocations;

    public function __construct(int $totalEvents, int $totalLocations)
    {
        $this->totalEvents = $totalEvents;
        $this->totalLocations = $totalLocations;
    }
}
