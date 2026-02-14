<?php

namespace App\Models\ViewModels\Dance;

use App\Models\ViewModels\Shared\ScheduleViewModel;

class DanceIndexViewModel
{
    public ScheduleViewModel $schedule;
    public DanceBannerStatsViewModel $bannerStats;
    public array $venues;

    public function __construct(ScheduleViewModel $schedule, DanceBannerStatsViewModel $bannerStats,array $venues) {
        $this->schedule = $schedule;
        $this->bannerStats = $bannerStats;
        $this->venues = $venues;
    }
}
