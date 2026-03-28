<?php

namespace App\Service\Cms\Interfaces;

use App\Models\Dance\DanceDetailEditorData;
use App\Models\Page\Page;
use App\Models\Requests\UpdateDanceDetailRequest;
use App\Models\Requests\UpdateDanceHomeRequest;

interface ICmsDanceService
{
    public function getDanceHomePage(): Page;
    public function saveDanceHomePage(UpdateDanceHomeRequest $request): void;
    public function getDanceDetailEditorData(string $pageSlug): DanceDetailEditorData;
    public function saveDanceDetailPage(string $pageSlug, UpdateDanceDetailRequest $request): void;
}
