<?php

namespace App\Repository\Interfaces;

use App\Models\Event\EventDetailPageModel;
use App\Models\Event\EventModel;

interface IDanceRepository
{
    public function findEventByName(string $eventName): ?EventModel;
    public function getVenuesByEventId(int $eventId): array;
    public function getPerformersByEventId(int $eventId): array;
    public function getDetailPagesByEventId(int $eventId): array;
    public function findDetailPageByPublicSlug(string $publicSlug): ?EventDetailPageModel;
    public function findDetailPageByCmsSlug(string $cmsSlug): ?EventDetailPageModel;
}
