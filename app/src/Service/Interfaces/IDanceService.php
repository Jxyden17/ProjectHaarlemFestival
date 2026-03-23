<?php

namespace App\Service\Interfaces;

use App\Models\Dance\DanceDetailData;
use App\Models\Dance\DanceIndexData;
use App\Models\Event\EventDetailPageModel;
use App\Models\Page\Page;

interface IDanceService
{
    public function getDanceIndexData(): DanceIndexData;
    public function getDanceDetailData(EventDetailPageModel $detailMeta): DanceDetailData;
    public function getDanceHomePage(): Page;
    public function getDanceDetailPageBySlug(string $pageSlug): ?EventDetailPageModel;
    public function getDanceDetailPages(): array;
}
