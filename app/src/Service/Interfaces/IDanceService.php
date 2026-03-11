<?php

namespace App\Service\Interfaces;

use App\Models\Event\EventDetailPageModel;
use App\Models\Page\Page;

interface IDanceService
{
    public function getDanceScheduleTitle(): string;
    public function getDanceIndexData(): array;
    public function getDanceVenues(): array;
    public function getDancePerformers(): array;
    public function getDanceHomePage(): Page;
    public function getDanceDetailPage(string $slug): Page;
    public function getDanceDetailPageBySlug(string $pageSlug): ?EventDetailPageModel;
    public function getPublishedDanceDetailPages(): array;
}
