<?php

namespace App\Models\Requests\Cms;

class DanceHomeContentRequest
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

    private function __construct(
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

    public static function fromArray(array $input): self
    {
        return new self(
            (string)($input['schedule_title'] ?? ''),
            (string)($input['banner_badge'] ?? ''),
            (string)($input['banner_title'] ?? ''),
            (string)($input['banner_description'] ?? ''),
            (string)($input['artists_title'] ?? ''),
            is_array($input['artists'] ?? null) ? $input['artists'] : [],
            (string)($input['important_information_title'] ?? ''),
            (string)($input['important_information_html'] ?? ''),
            (string)($input['passes_title'] ?? ''),
            is_array($input['passes'] ?? null) ? $input['passes'] : [],
            (string)($input['capacity_title'] ?? ''),
            (string)($input['capacity_html'] ?? ''),
            (string)($input['special_title'] ?? ''),
            (string)($input['special_html'] ?? '')
        );
    }

    public function toArray(): array
    {
        return [
            'schedule_title' => $this->scheduleTitle,
            'banner_badge' => $this->bannerBadge,
            'banner_title' => $this->bannerTitle,
            'banner_description' => $this->bannerDescription,
            'artists_title' => $this->artistsTitle,
            'artists' => $this->artists,
            'important_information_title' => $this->importantInformationTitle,
            'important_information_html' => $this->importantInformationHtml,
            'passes_title' => $this->passesTitle,
            'passes' => $this->passes,
            'capacity_title' => $this->capacityTitle,
            'capacity_html' => $this->capacityHtml,
            'special_title' => $this->specialTitle,
            'special_html' => $this->specialHtml,
        ];
    }

    public function scheduleTitle(): string
    {
        return trim($this->scheduleTitle);
    }

    public function bannerTitle(): string
    {
        return trim($this->bannerTitle);
    }

    public function bannerDescription(): string
    {
        return trim($this->bannerDescription);
    }

    public function artistsTitle(): string
    {
        return trim($this->artistsTitle);
    }

    public function artists(): array
    {
        return $this->artists;
    }

    public function importantInformationTitle(): string
    {
        return trim($this->importantInformationTitle);
    }

    public function importantInformationHtml(): string
    {
        return trim($this->importantInformationHtml);
    }

    public function passesTitle(): string
    {
        return trim($this->passesTitle);
    }

    public function passes(): array
    {
        return $this->passes;
    }

    public function capacityTitle(): string
    {
        return trim($this->capacityTitle);
    }

    public function capacityHtml(): string
    {
        return trim($this->capacityHtml);
    }

    public function specialTitle(): string
    {
        return trim($this->specialTitle);
    }

    public function specialHtml(): string
    {
        return trim($this->specialHtml);
    }
}
