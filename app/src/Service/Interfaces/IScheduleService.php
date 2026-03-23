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

    public function getSessionById(int $id): ?ScheduleEditorViewModel;
    public function editSchedule(int $id, int $eventId, int $venueId, string $date, string $startTime, int $availableSpots, ?string $label, ?float $price, ?int $language): bool;
    public function createSchedule(int $eventId, int $venueId, string $date, string $startTime, int $availableSpots, ?string $label, ?float $price, ?int $language, array $performerIds = []): bool;
    public function deleteSchedule(int $id): bool;
}
