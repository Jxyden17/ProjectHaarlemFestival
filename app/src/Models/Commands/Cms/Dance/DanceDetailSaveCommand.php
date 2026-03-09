<?php

namespace App\Models\Commands\Cms\Dance;

class DanceDetailSaveCommand
{
    private string $heroTitle;
    private string $heroBadge;
    private string $heroSubtitle;
    private array $heroImages;
    private string $highlightsTitle;
    private array $highlights;
    private string $tracksTitle;
    private string $tracksNote;
    private array $tracks;
    private string $importantInformationTitle;
    private string $importantInformationHtml;

    public function __construct(
        string $heroTitle,
        string $heroBadge,
        string $heroSubtitle,
        array $heroImages,
        string $highlightsTitle,
        array $highlights,
        string $tracksTitle,
        string $tracksNote,
        array $tracks,
        string $importantInformationTitle,
        string $importantInformationHtml
    ) {
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

    public function heroTitle(): string { return $this->heroTitle; }
    public function heroBadge(): string { return $this->heroBadge; }
    public function heroSubtitle(): string { return $this->heroSubtitle; }
    public function heroImages(): array { return $this->heroImages; }
    public function highlightsTitle(): string { return $this->highlightsTitle; }
    public function highlights(): array { return $this->highlights; }
    public function tracksTitle(): string { return $this->tracksTitle; }
    public function tracksNote(): string { return $this->tracksNote; }
    public function tracks(): array { return $this->tracks; }
    public function importantInformationTitle(): string { return $this->importantInformationTitle; }
    public function importantInformationHtml(): string { return $this->importantInformationHtml; }
}
