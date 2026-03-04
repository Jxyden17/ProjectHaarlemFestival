<?php

namespace App\Service\Interfaces;

use App\Models\Commands\Cms\Dance\DanceHomeSaveCommand;
use App\Models\Page\Page;
use App\Models\ViewModels\Cms\Dance\DanceHomeContentViewModel;
use App\Models\ViewModels\Dance\DanceBannerStatsViewModel;

interface IDanceService
{
    public function getDanceBannerStats(): DanceBannerStatsViewModel;
    public function getDanceVenues(): array;
    public function getDancePerformers(): array;
    public function getDanceHomePage(): Page;
    public function getDanceHomeFormData(): DanceHomeContentViewModel;
    public function saveDanceHomePage(DanceHomeSaveCommand $command): void;
}
