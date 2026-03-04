<?php

namespace App\Service\Interfaces;

use App\Models\ViewModels\Cms\Schedule\ScheduleEditorViewModel;

interface ICmsEventEditorService
{
    public function getEditorData(string $eventName): ScheduleEditorViewModel;

    public function mergePostedEditorData(
        string $eventName,
        ScheduleEditorViewModel $editorData,
        array $postedVenues,
        array $postedPerformers,
        array $postedSessions
    ): ScheduleEditorViewModel;
}
