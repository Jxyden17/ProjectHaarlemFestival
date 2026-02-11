<?php

namespace App\Service\Interfaces;

use App\Models\ViewModels\ScheduleViewModel;

interface IScheduleService
{
    public function getDanceScheduleData(): ScheduleViewModel;
    public function getDanceVenues(): array;
}
