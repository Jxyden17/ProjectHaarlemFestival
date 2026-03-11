<?php

namespace App\Repository\Interfaces;

use App\Models\Event\EventModel;
use App\Models\Page\Page;
use App\Models\Event\VenueModel;

interface IYummyRepository
{
    public function findYummyEvent(): ?EventModel;

    public function getVenuesByEventId(int $eventId): array;

    public function getPageBySlug(string $slug): ?Page;

    public function getSectionItems(int $sectionId): array;

    public function getVenueBySlug(string $slug): ?VenueModel;
}