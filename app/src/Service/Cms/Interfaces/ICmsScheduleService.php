<?php

namespace App\Service\Cms\Interfaces;

use App\Models\Commands\Cms\Schedule\ScheduleSaveCommand;

interface ICmsScheduleService
{
    public function saveScheduleData(string $eventName, ScheduleSaveCommand $command): void;
}
