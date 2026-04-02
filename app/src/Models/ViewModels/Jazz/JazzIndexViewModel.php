<?php

namespace App\Models\ViewModels\Jazz;

use App\Models\ViewModels\Shared\ScheduleViewModel;
use App\Models\PerformerModel;
use App\Models\Page\Page;

class JazzIndexViewModel
{
    public ScheduleViewModel $schedule;
    public array $jazzPerformers;
    public Page $page;
    

    public function __construct(ScheduleViewModel $schedule, array $jazzPerformers,Page $page) {
        $this->schedule = $schedule;
        $this->jazzPerformers = $jazzPerformers;
        $this->page = $page;
    }
}
