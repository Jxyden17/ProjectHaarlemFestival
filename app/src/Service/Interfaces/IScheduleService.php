<?php

namespace App\Service\Interfaces;

use App\Models\ViewModels\Shared\ScheduleViewModel;

interface IScheduleService
{
    public function getScheduleDataForEvent(string $eventName, string $title): ScheduleViewModel;
    public function getScheduleDataForAllEvents(string $title): ScheduleViewModel;
}
