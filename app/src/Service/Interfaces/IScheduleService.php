<?php

namespace App\Service\Interfaces;

use App\Models\Event\EventModel;
use App\Models\Schedule\ScheduleData;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorViewModel;

interface IScheduleService
{
    public function getScheduleDataForEvent(string $eventName, string $title): ScheduleData;

    public function getScheduleDataForAllEvents(string $title): ScheduleData;

    public function getEventResources(string $eventName, string $title): ?array;

    public function getScheduleSessionsByPerformerName(string $eventName, string $performerName): array;
    public function getSessionById(int $id): ?ScheduleEditorViewModel;
    public function editSchedule(int $id, int $eventId, int $venueId, string $date, string $startTime, int $availableSpots, ?string $label, ?float $price, ?int $language): bool;
    public function createSchedule(int $eventId, int $venueId, string $date, string $startTime, int $availableSpots, ?string $label, ?float $price, ?int $language, array $performerIds = []): bool;
    public function deleteSchedule(int $id): bool;
    public function getScheduleSessionsByPerformerId(string $eventName, int $performerId): array;
    public function findEventByName(string $eventName): ?EventModel;
}