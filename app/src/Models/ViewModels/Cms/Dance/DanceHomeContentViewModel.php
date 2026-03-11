<?php

namespace App\Models\ViewModels\Cms\Dance;

class DanceHomeContentViewModel
{
    public string $pageTitle;
    public string $scheduleTitle;
    public string $bannerBadge;
    public string $bannerTitle;
    public string $bannerDescription;
    public string $artistsTitle;
    public array $artists;
    public string $importantInformationTitle;
    public string $importantInformationHtml;
    public string $passesTitle;
    public array $passes;
    public string $capacityTitle;
    public string $capacityHtml;
    public string $specialTitle;
    public string $specialHtml;

    public function __construct(
        string $pageTitle,
        string $scheduleTitle,
        string $bannerBadge,
        string $bannerTitle,
        string $bannerDescription,
        string $artistsTitle,
        array $artists,
        string $importantInformationTitle,
        string $importantInformationHtml,
        string $passesTitle,
        array $passes,
        string $capacityTitle,
        string $capacityHtml,
        string $specialTitle,
        string $specialHtml
    ) {
        $this->pageTitle = $pageTitle;
        $this->scheduleTitle = $scheduleTitle;
        $this->bannerBadge = $bannerBadge;
        $this->bannerTitle = $bannerTitle;
        $this->bannerDescription = $bannerDescription;
        $this->artistsTitle = $artistsTitle;
        $this->artists = $artists;
        $this->importantInformationTitle = $importantInformationTitle;
        $this->importantInformationHtml = $importantInformationHtml;
        $this->passesTitle = $passesTitle;
        $this->passes = $passes;
        $this->capacityTitle = $capacityTitle;
        $this->capacityHtml = $capacityHtml;
        $this->specialTitle = $specialTitle;
        $this->specialHtml = $specialHtml;
    }
}
