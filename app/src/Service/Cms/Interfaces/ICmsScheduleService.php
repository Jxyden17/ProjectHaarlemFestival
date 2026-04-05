<?php

namespace App\Service\Cms\Interfaces;

use App\Models\Edit\Schedule\ScheduleSaveInput;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorViewModel;

interface ICmsScheduleService
{
    // Returns the CMS schedule editor payload for one event so the admin form can preload sessions, venues, and performers. Example: event 'Dance' -> ScheduleEditorViewModel.
    public function getScheduleEditorData(string $eventName): ScheduleEditorViewModel;

    // Saves one event schedule payload so validated CMS edits update venues, performers, sessions, and links together.
    public function saveScheduleData(string $eventName, ScheduleSaveInput $input): void;
}
