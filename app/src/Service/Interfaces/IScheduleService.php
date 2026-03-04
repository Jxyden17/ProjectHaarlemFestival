<?php

namespace App\Service\Interfaces;

use App\Models\Commands\Cms\Schedule\ScheduleSaveCommand;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorViewModel;
use App\Models\ViewModels\Shared\ScheduleViewModel;

interface IScheduleService
{
    public function getScheduleDataForEvent(string $eventName, string $title): ScheduleViewModel;
    public function getScheduleEditorData(string $eventName): ScheduleEditorViewModel;
    public function saveScheduleData(string $eventName, ScheduleSaveCommand $command): void;
}
