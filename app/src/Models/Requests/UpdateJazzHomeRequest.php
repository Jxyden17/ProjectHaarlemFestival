<?php

namespace App\Models\Requests;

use App\Models\Edit\Dance\DanceHomePassEditRow;

class UpdateJazzHomeRequest
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
            trim((string)($input['page_title'] ?? '')),
            trim((string)($input['schedule_title'] ?? '')),
            trim((string)($input['featured_artists_title'] ?? '')),
            trim((string)($input['banner_badge'] ?? '')),
            trim((string)($input['banner_title'] ?? '')),
            trim((string)($input['banner_description'] ?? '')),
            trim((string)($input['important_information_title'] ?? '')),
            trim((string)($input['important_information_html'] ?? '')),
            trim((string)($input['passes_title'] ?? '')),
            self::mapPasses(is_array($input['passes'] ?? null) ? $input['passes'] : []),
            trim((string)($input['capacity_title'] ?? '')),
            trim((string)($input['capacity_html'] ?? '')),
            trim((string)($input['special_title'] ?? '')),
            trim((string)($input['special_html'] ?? ''))
        );
    }

    public function pageTitle(): string
    {
        return $this->pageTitle;
    }

    private static function mapPasses(array $input): array
    {
        $rows = [];
        foreach ($input as $row) {
            if (!is_array($row)) {
                continue;
            }

            $rows[] = DanceHomePassEditRow::fromArray($row);
        }

        return $rows;
    }

    public function scheduleTitle(): string
    {
        return $this->scheduleTitle;
    }

    public function featuredArtistsTitle(): string
    {
        return $this->featuredArtistsTitle;
    }

    public function bannerTitle(): string
    {
        return $this->bannerTitle;
    }

    public function bannerBadge(): string
    {
        return $this->bannerBadge;
    }

    public function bannerDescription(): string
    {
        return $this->bannerDescription;
    }

    public function importantInformationTitle(): string
    {
        return $this->importantInformationTitle;
    }

    public function importantInformationHtml(): string
    {
        return $this->importantInformationHtml;
    }

    public function passesTitle(): string
    {
        return $this->passesTitle;
    }

    public function passes(): array
    {
        return $this->passes;
    }

    public function capacityTitle(): string
    {
        return $this->capacityTitle;
    }

    public function capacityHtml(): string
    {
        return $this->capacityHtml;
    }

    public function specialTitle(): string
    {
        return $this->specialTitle;
    }

    public function specialHtml(): string
    {
        return $this->specialHtml;
    }
}
