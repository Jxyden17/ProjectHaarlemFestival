<?php

namespace App\Service\Interfaces;

use App\Models\ViewModels\Dance\DanceBannerStatsViewModel;

interface IDanceService
{
    public function getDanceBannerStats(): DanceBannerStatsViewModel;
    public function getDanceVenues(): array;
}
