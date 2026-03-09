<?php

namespace App\Service\Interfaces;

use App\Models\Commands\Cms\Dance\DanceHomeSaveCommand;
use App\Models\Commands\Cms\Dance\DanceDetailSaveCommand;
use App\Models\ViewModels\Cms\Dance\DanceDetailContentViewModel;
use App\Models\ViewModels\Cms\Dance\DanceHomeContentViewModel;

interface ICmsDanceService
{
    public function getDanceHomeFormData(): DanceHomeContentViewModel;
    public function saveDanceHomePage(DanceHomeSaveCommand $command): void;
    public function getDanceDetailFormData(string $detailSlug): DanceDetailContentViewModel;
    public function saveDanceDetailPage(string $detailSlug, DanceDetailSaveCommand $command): void;
}
