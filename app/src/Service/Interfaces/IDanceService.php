<?php

namespace App\Service\Interfaces;

use App\Models\Dance\DanceDetailPageInput;
use App\Models\Dance\DanceIndexPageInput;
use App\Models\Event\EventDetailPageModel;
use App\Models\Page\Page;

interface IDanceService
{
    public function getDanceIndexPage(): DanceIndexPageInput;
    public function getDanceDetailPage(EventDetailPageModel $detailMeta): DanceDetailPageInput;
    public function getDanceHomeContentPage(): Page;
    public function getDanceDetailPageBySlug(string $pageSlug): ?EventDetailPageModel;
    public function getPublishedDanceDetailPages(): array;
}
