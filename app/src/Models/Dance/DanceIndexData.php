<?php

namespace App\Models\Dance;

use App\Models\Page\Page;
use App\Models\Schedule\ScheduleData;

class DanceIndexData
{
    public Page $homePage;
    public ScheduleData $schedule;
    public array $performers;
    public array $detailPages;
    public array $venues;
    public array $passSessions;

    public function __construct(Page $homePage, ScheduleData $schedule, array $performers, array $detailPages, array $venues, array $passSessions = [])
    {
        $this->homePage = $homePage;
        $this->schedule = $schedule;
        $this->performers = $performers;
        $this->detailPages = $detailPages;
        $this->venues = $venues;
        $this->passSessions = $passSessions;
    }
}
