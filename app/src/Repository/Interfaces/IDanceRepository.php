<?php

namespace App\Repository\Interfaces;

use App\Models\Event\EventDetailPageModel;
use App\Models\Event\EventModel;

interface IDanceRepository
{
    public function findDanceEvent(): ?EventModel;
    public function countSessionsByEventId(int $eventId): int;
    public function countDistinctVenuesByEventId(int $eventId): int;
    public function getVenuesByEventId(int $eventId): array;
    public function getPerformersByEventId(int $eventId): array;
    public function findDetailPageByPublicSlug(string $publicSlug): ?EventDetailPageModel;
    public function findDetailPageByCmsSlug(string $cmsSlug): ?EventDetailPageModel;
    public function getPublishedDetailPagesByEventId(int $eventId): array;
}
