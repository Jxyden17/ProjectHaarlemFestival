<?php

namespace App\Repository\Interfaces;

use App\Models\Event\EventDetailPageModel;

interface IDanceRepository
{
    // Returns all dance detail page records for one event so services can build navigation and listings.
    public function getDetailPagesByEventId(int $eventId): array;
    // Finds one dance detail page record by page slug so callers can branch on null when it does not exist. Example: slug 'urban-echo' -> EventDetailPageModel.
    public function findDetailPageByPageSlug(string $pageSlug): ?EventDetailPageModel;
}
