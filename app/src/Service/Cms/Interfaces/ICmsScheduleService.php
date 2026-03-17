<?php

namespace App\Service\Cms\Interfaces;

use App\Models\Edit\Schedule\ScheduleSaveInput;

interface ICmsScheduleService
{
    public function saveScheduleData(string $eventName, ScheduleSaveInput $input): void;
}
