<?php

namespace App\Models\Commands\Cms\Dance;

class DanceHomeSaveCommand
{
    private string $scheduleTitle;
    private string $bannerBadge;
    private string $bannerTitle;
    private string $bannerDescription;
    private string $artistsTitle;
    private array $artists;
    private string $importantInformationTitle;
    private string $importantInformationHtml;
    private string $passesTitle;
    private array $passes;
    private string $capacityTitle;
    private string $capacityHtml;
    private string $specialTitle;
    private string $specialHtml;

    public function __construct(
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

    public function scheduleTitle(): string { return $this->scheduleTitle; }
    public function bannerBadge(): string { return $this->bannerBadge; }
    public function bannerTitle(): string { return $this->bannerTitle; }
    public function bannerDescription(): string { return $this->bannerDescription; }
    public function artistsTitle(): string { return $this->artistsTitle; }
    public function artists(): array { return $this->artists; }
    public function importantInformationTitle(): string { return $this->importantInformationTitle; }
    public function importantInformationHtml(): string { return $this->importantInformationHtml; }
    public function passesTitle(): string { return $this->passesTitle; }
    public function passes(): array { return $this->passes; }
    public function capacityTitle(): string { return $this->capacityTitle; }
    public function capacityHtml(): string { return $this->capacityHtml; }
    public function specialTitle(): string { return $this->specialTitle; }
    public function specialHtml(): string { return $this->specialHtml; }
}
