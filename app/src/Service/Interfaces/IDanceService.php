<?php

namespace App\Service\Interfaces;

use App\Models\Page\Page;
use App\Models\ViewModels\Dance\DanceBannerStatsViewModel;

interface IDanceService
{
    public function getDanceBannerStats(): DanceBannerStatsViewModel;
    public function getDanceVenues(): array;
    public function getDanceHomePage(): Page;
    public function saveDanceHomePage(array $input): void;
}
