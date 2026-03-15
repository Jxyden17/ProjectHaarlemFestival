<?php

namespace App\Service\Cms\Interfaces;

use App\Models\Requests\Cms\DanceDetailContentRequest;
use App\Models\Requests\Cms\DanceHomeContentRequest;
use App\Models\ViewModels\Cms\Dance\DanceDetailContentViewModel;
use App\Models\ViewModels\Cms\Dance\DanceHomeContentViewModel;

interface ICmsDanceService
{
    public function getDanceHomeFormData(): DanceHomeContentViewModel;
    public function saveDanceHomePage(DanceHomeContentRequest $request): void;
    public function getDanceDetailFormData(string $pageSlug): DanceDetailContentViewModel;
    public function saveDanceDetailPage(string $pageSlug, DanceDetailContentRequest $request): void;
}
