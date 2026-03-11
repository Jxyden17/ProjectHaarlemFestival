<?php

namespace App\Service\Interfaces;

use App\Models\ViewModels\Cms\Schedule\ScheduleEditorViewModel;
use App\Models\ViewModels\Shared\ScheduleViewModel;

interface IScheduleService
{
    public function getScheduleDataForEvent(string $eventName, string $title): ScheduleViewModel;

    public function getScheduleDataForAllEvents(string $title): ScheduleViewModel;

    public function getScheduleRowsByPerformerName(string $eventName, string $performerName): array;

    public function getScheduleRowsByPerformerId(string $eventName, int $performerId): array;

    public function getScheduleEditorData(string $eventName): ScheduleEditorViewModel;
}
