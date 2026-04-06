<?php

namespace App\Models\ViewModels\Cms\Dance;

class DanceDetailEditViewModel
{
    public string $pageSlug;
    public string $editorTitle;
    public string $publicPath;
    public string $pageTitle;
    public string $performerName;
    public string $heroTitle;
    public string $heroBadge;
    public string $heroSubtitle;
    public array $heroImages;
    public string $highlightsTitle;
    public array $highlights;
    public string $tracksTitle;
    public string $tracksNote;
    public array $tracks;
    public string $scheduleTitle;
    public string $importantInformationTitle;
    public string $importantInformationHtml;

    public function __construct(string $pageSlug, string $editorTitle, string $publicPath, string $pageTitle, string $performerName, string $heroTitle, string $heroBadge, string $heroSubtitle, array $heroImages, string $highlightsTitle, array $highlights, string $tracksTitle, string $tracksNote, array $tracks, string $scheduleTitle, string $importantInformationTitle, string $importantInformationHtml) {
        $this->pageSlug = $pageSlug;
        $this->editorTitle = $editorTitle;
        $this->publicPath = $publicPath;
        $this->pageTitle = $pageTitle;
        $this->performerName = $performerName;
        $this->heroTitle = $heroTitle;
        $this->heroBadge = $heroBadge;
        $this->heroSubtitle = $heroSubtitle;
        $this->heroImages = $heroImages;
        $this->highlightsTitle = $highlightsTitle;
        $this->highlights = $highlights;
        $this->tracksTitle = $tracksTitle;
        $this->tracksNote = $tracksNote;
        $this->tracks = $tracks;
        $this->scheduleTitle = $scheduleTitle;
        $this->importantInformationTitle = $importantInformationTitle;
        $this->importantInformationHtml = $importantInformationHtml;
    }
}
