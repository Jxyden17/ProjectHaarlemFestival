<?php

namespace App\Models\Dance;

class DanceDetailEditInput
{
    public string $pageTitle;
    public string $heroTitle;
    public string $heroBadge;
    public string $heroSubtitle;
    public array $heroImages;
    public string $highlightsTitle;
    public array $highlights;
    public string $tracksTitle;
    public string $tracksNote;
    public array $tracks;
    public string $importantInformationTitle;
    public string $importantInformationHtml;

    public function __construct(string $pageTitle, string $heroTitle, string $heroBadge, string $heroSubtitle, array $heroImages, string $highlightsTitle, array $highlights, string $tracksTitle, string $tracksNote, array $tracks, string $importantInformationTitle, string $importantInformationHtml)
    {
        $this->pageTitle = $pageTitle;
        $this->heroTitle = $heroTitle;
        $this->heroBadge = $heroBadge;
        $this->heroSubtitle = $heroSubtitle;
        $this->heroImages = $heroImages;
        $this->highlightsTitle = $highlightsTitle;
        $this->highlights = $highlights;
        $this->tracksTitle = $tracksTitle;
        $this->tracksNote = $tracksNote;
        $this->tracks = $tracks;
        $this->importantInformationTitle = $importantInformationTitle;
        $this->importantInformationHtml = $importantInformationHtml;
    }
}
