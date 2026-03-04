<?php

namespace App\Models\ViewModels\Dance;

use App\Models\Page\Page;
use App\Models\ViewModels\Shared\ScheduleViewModel;

class DanceIndexViewModel
{
    public ScheduleViewModel $schedule;
    public DanceBannerStatsViewModel $bannerStats;
    public array $venues;
    public array $performers;
    public Page $homeContent;

    public function __construct(
        ScheduleViewModel $schedule,
        DanceBannerStatsViewModel $bannerStats,
        array $venues,
        array $performers,
        Page $homeContent
    ) {
        $this->schedule = $schedule;
        $this->bannerStats = $bannerStats;
        $this->venues = $venues;
        $this->performers = $performers;
        $this->homeContent = $homeContent;
    }
}
