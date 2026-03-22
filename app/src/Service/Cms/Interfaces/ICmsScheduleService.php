<?php

namespace App\Service\Cms\Interfaces;

use App\Models\Edit\Schedule\ScheduleSaveInput;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorViewModel;

interface ICmsScheduleService
{
    public function getScheduleEditorData(string $eventName): ScheduleEditorViewModel;

    public function saveScheduleData(string $eventName, ScheduleSaveInput $input): void;
}
