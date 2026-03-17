<?php

namespace App\Service\Cms\Interfaces;

use App\Models\Requests\Cms\UpdateDanceDetailRequest;
use App\Models\Requests\Cms\UpdateDanceHomeRequest;
use App\Models\ViewModels\Cms\Dance\DanceDetailEditViewModel;
use App\Models\ViewModels\Cms\Dance\DanceHomeEditViewModel;

interface ICmsDanceService
{
    public function getDanceHomeFormData(): DanceHomeEditViewModel;
    public function saveDanceHomePage(UpdateDanceHomeRequest $request): void;
    public function getDanceDetailFormData(string $pageSlug): DanceDetailEditViewModel;
    public function saveDanceDetailPage(string $pageSlug, UpdateDanceDetailRequest $request): void;
}
