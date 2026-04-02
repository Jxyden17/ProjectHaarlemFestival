<?php

namespace App\Service\Interfaces;

use App\Models\Dance\DanceDetailData;
use App\Models\Dance\DanceIndexData;
use App\Models\Event\EventDetailPageModel;
use App\Models\Page\Page;

interface IDanceService
{
    // Returns the full public dance landing payload so the controller gets one typed bundle instead of many lookups.
    public function getDanceIndexData(): DanceIndexData;
    // Returns one public dance detail payload for a slug so missing pages can cleanly return null. Example: slug 'urban-echo' -> DanceDetailData.
    public function getDanceDetailData(string $pageSlug): ?DanceDetailData;
    // Returns the dance home content page so public and CMS flows share the same page source. Example: slug 'dance-home' -> Page.
    public function getDanceHomePage(): Page;
    // Finds dance detail metadata by slug so upload services can resolve page-specific media targets. Example: slug 'urban-echo' -> EventDetailPageModel.
    public function getDanceDetailPageBySlug(string $pageSlug): ?EventDetailPageModel;
    // Lists all registered dance detail pages so CMS navigation can link to each editor entry.
    public function getDanceDetailPages(): array;
}
