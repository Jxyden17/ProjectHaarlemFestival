<?php

namespace App\Service\Interfaces;

use App\Models\Event\EventDetailPageModel;
use App\Models\Page\Page;
use App\Models\ViewModels\Dance\DanceBannerStatsViewModel;

interface IDanceService
{
    public function getDanceScheduleTitle(): string;
    public function getDanceBannerStats(): DanceBannerStatsViewModel;
    public function getDanceVenues(): array;
    public function getDancePerformers(): array;
    public function getDanceHomePage(): Page;
    public function getDanceDetailPage(string $slug): Page;
    public function getDanceDetailPageByPublicSlug(string $publicSlug): ?EventDetailPageModel;
    public function getPublishedDanceDetailPages(): array;
}
