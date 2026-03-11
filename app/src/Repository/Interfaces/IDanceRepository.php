<?php

namespace App\Repository\Interfaces;

use App\Models\Event\EventDetailPageModel;

interface IDanceRepository
{
    public function getDetailPagesByEventId(int $eventId): array;
    public function findDetailPageBySlug(string $detailSlug): ?EventDetailPageModel;
}
