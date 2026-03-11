<?php

namespace App\Models\ViewModels\Dance;

use App\Models\ViewModels\Shared\ScheduleViewModel;

class DanceIndexViewModel
{
    public ScheduleViewModel $schedule;
    public string $bannerBadge;
    public string $bannerTitle;
    public string $bannerDescription;
    public int $totalEvents;
    public int $totalLocations;
    public string $artistsTitle;
    public array $artistCards;
    public string $importantInfoTitle;
    public string $importantInfoHtml;
    public string $passesTitle;
    public array $passes;
    public string $capacityTitle;
    public string $capacityHtml;
    public string $specialTitle;
    public string $specialHtml;
    public array $venues;

    public function __construct(
        ScheduleViewModel $schedule,
        ?string $bannerBadge = null,
        ?string $bannerTitle = null,
        ?string $bannerDescription = null,
        int $totalEvents = 0,
        int $totalLocations = 0,
        ?string $artistsTitle = null,
        ?array $artistCards = null,
        ?string $importantInfoTitle = null,
        ?string $importantInfoHtml = null,
        ?string $passesTitle = null,
        ?array $passes = null,
        ?string $capacityTitle = null,
        ?string $capacityHtml = null,
        ?string $specialTitle = null,
        ?string $specialHtml = null,
        ?array $venues = null
    ) {
        $this->schedule = $schedule;
        $this->bannerBadge = trim((string)$bannerBadge);
        $this->bannerTitle = trim((string)$bannerTitle);
        $this->bannerDescription = (string)$bannerDescription;
        $this->totalEvents = $totalEvents;
        $this->totalLocations = $totalLocations;
        $this->artistsTitle = trim((string)$artistsTitle);
        $this->artistCards = $artistCards ?? [];
        $this->importantInfoTitle = $this->normalizeText($importantInfoTitle, 'Important Information');
        $this->importantInfoHtml = (string)$importantInfoHtml;
        $this->passesTitle = $this->normalizeText($passesTitle, 'Passes');
        $this->passes = $passes ?? [];
        $this->capacityTitle = $this->normalizeText($capacityTitle, 'Venue Capacity');
        $this->capacityHtml = (string)$capacityHtml;
        $this->specialTitle = $this->normalizeText($specialTitle, 'Special Sessions');
        $this->specialHtml = (string)$specialHtml;
        $this->venues = $venues ?? [];
    }

    private function normalizeText(?string $value, string $fallback): string
    {
        $normalized = trim((string)$value);

        return $normalized === '' ? $fallback : $normalized;
    }
}
