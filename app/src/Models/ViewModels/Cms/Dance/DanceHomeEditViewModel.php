<?php

namespace App\Models\ViewModels\Cms\Dance;

class DanceHomeEditViewModel
{
    public string $pageTitle;
    public string $scheduleTitle;
    public string $featuredArtistsTitle;
    public string $bannerBadge;
    public string $bannerTitle;
    public string $bannerDescription;
    public string $importantInformationTitle;
    public string $importantInformationHtml;
    public string $passesTitle;
    public array $passes;
    public string $capacityTitle;
    public string $capacityHtml;
    public string $specialTitle;
    public string $specialHtml;

    public function __construct(string $pageTitle, string $scheduleTitle, string $featuredArtistsTitle, string $bannerBadge, string $bannerTitle, string $bannerDescription, string $importantInformationTitle, string $importantInformationHtml, string $passesTitle, array $passes, string $capacityTitle, string $capacityHtml, string $specialTitle, string $specialHtml) {
        $this->pageTitle = $pageTitle;
        $this->scheduleTitle = $scheduleTitle;
        $this->featuredArtistsTitle = $featuredArtistsTitle;
        $this->bannerBadge = $bannerBadge;
        $this->bannerTitle = $bannerTitle;
        $this->bannerDescription = $bannerDescription;
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
