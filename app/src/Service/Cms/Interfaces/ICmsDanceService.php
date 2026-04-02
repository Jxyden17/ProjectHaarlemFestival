<?php

namespace App\Service\Cms\Interfaces;

use App\Models\Dance\DanceDetailEditorData;
use App\Models\Page\Page;
use App\Models\Requests\UpdateDanceDetailRequest;
use App\Models\Requests\UpdateDanceHomeRequest;

interface ICmsDanceService
{
    // Returns the dance home page for the CMS editor so the form can be prefilled from stored content.
    public function getDanceHomePage(): Page;
    // Saves sanitized dance home content so CMS edits become the persisted page payload.
    public function saveDanceHomePage(UpdateDanceHomeRequest $request): void;
    // Returns CMS editor data for one dance detail page so the edit view has metadata and content together. Example: slug 'urban-echo' -> DanceDetailEditorData.
    public function getDanceDetailEditorData(string $pageSlug): DanceDetailEditorData;
    // Saves one dance detail page so CMS edits update the correct stored sections and items.
    public function saveDanceDetailPage(string $pageSlug, UpdateDanceDetailRequest $request): void;
}
