<?php

namespace App\Models\ViewModels\Jazz;

use App\Models\ViewModels\Shared\ScheduleViewModel;
use App\Models\PerformerModel;

class JazzIndexViewModel
{
    public ScheduleViewModel $schedule;
    public array $jazzPerformers;
    

    public function __construct(ScheduleViewModel $schedule, array $jazzPerformers) {
        $this->schedule = $schedule;
        $this->jazzPerformers = $jazzPerformers;
    }
}
