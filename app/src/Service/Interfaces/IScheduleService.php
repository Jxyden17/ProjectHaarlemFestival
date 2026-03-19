<?php

namespace App\Service\Interfaces;

use App\Models\Event\EventModel;
use App\Models\Schedule\ScheduleData;

interface IScheduleService
{
    public function getScheduleDataForEvent(string $eventName, string $title): ScheduleData;

    public function getScheduleDataForAllEvents(string $title): ScheduleData;

    public function getEventResources(string $eventName, string $title): ?array;

    public function getScheduleSessionsByPerformerName(string $eventName, string $performerName): array;

    public function getScheduleSessionsByPerformerId(string $eventName, int $performerId): array;

    public function findEventByName(string $eventName): ?EventModel;
}
