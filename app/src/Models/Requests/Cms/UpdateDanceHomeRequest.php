<?php

namespace App\Models\Requests\Cms;

use App\Models\Requests\Cms\Dance\DanceHomePassRowRequest;

class UpdateDanceHomeRequest
{
    private string $pageTitle;
    private string $scheduleTitle;
    private string $featuredArtistsTitle;
    private string $bannerBadge;
    private string $bannerTitle;
    private string $bannerDescription;
    private string $importantInformationTitle;
    private string $importantInformationHtml;
    private string $passesTitle;
    private array $passes;
    private string $capacityTitle;
    private string $capacityHtml;
    private string $specialTitle;
    private string $specialHtml;

    private function __construct(string $pageTitle, string $scheduleTitle, string $featuredArtistsTitle, string $bannerBadge, string $bannerTitle, string $bannerDescription, string $importantInformationTitle, string $importantInformationHtml, string $passesTitle, array $passes, string $capacityTitle, string $capacityHtml, string $specialTitle, string $specialHtml) {
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

    public static function fromArray(array $input): self
    {
        return new self(
            (string)($input['page_title'] ?? ''),
            (string)($input['schedule_title'] ?? ''),
            (string)($input['featured_artists_title'] ?? ''),
            (string)($input['banner_badge'] ?? ''),
            (string)($input['banner_title'] ?? ''),
            (string)($input['banner_description'] ?? ''),
            (string)($input['important_information_title'] ?? ''),
            (string)($input['important_information_html'] ?? ''),
            (string)($input['passes_title'] ?? ''),
            self::mapPasses(is_array($input['passes'] ?? null) ? $input['passes'] : []),
            (string)($input['capacity_title'] ?? ''),
            (string)($input['capacity_html'] ?? ''),
            (string)($input['special_title'] ?? ''),
            (string)($input['special_html'] ?? '')
        );
    }

    public function pageTitle(): string
    {
        return trim($this->pageTitle);
    }

    private static function mapPasses(array $input): array
    {
        $rows = [];
        foreach ($input as $row) {
            if (!is_array($row)) {
                continue;
            }

            $rows[] = DanceHomePassRowRequest::fromArray($row);
        }

        return $rows;
    }

    public function scheduleTitle(): string
    {
        return trim($this->scheduleTitle);
    }

    public function featuredArtistsTitle(): string
    {
        return trim($this->featuredArtistsTitle);
    }

    public function bannerTitle(): string
    {
        return trim($this->bannerTitle);
    }

    public function bannerBadge(): string
    {
        return trim($this->bannerBadge);
    }

    public function bannerDescription(): string
    {
        return trim($this->bannerDescription);
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
