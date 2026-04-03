<?php

namespace App\Models\Requests;

use App\Models\Edit\Jazz\JazzHomePassEditRow;

class UpdateJazzHomeRequest
{
    private string $pageTitle;
    private string $scheduleTitle;
    private string $featuredArtistsTitle;
    private string $passesTitle;
    private array $passes;
    private function __construct(string $pageTitle, string $scheduleTitle, string $featuredArtistsTitle, string $passesTitle, array $passes) {
        $this->pageTitle = $pageTitle;
        $this->scheduleTitle = $scheduleTitle;
        $this->featuredArtistsTitle = $featuredArtistsTitle;
        $this->passesTitle = $passesTitle;
        $this->passes = $passes;
    }

    public static function fromArray(array $input): self
    {
        return new self(
            trim((string)($input['page_title'] ?? '')),
            trim((string)($input['schedule_title'] ?? '')),
            trim((string)($input['featured_artists_title'] ?? '')),
            trim((string)($input['passes_title'] ?? '')),
            self::mapPasses(is_array($input['passes'] ?? null) ? $input['passes'] : []),
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

            $rows[] = JazzHomePassEditRow::fromArray($row);
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

    public function passesTitle(): string
    {
        return $this->passesTitle;
    }

    public function passes(): array
    {
        return $this->passes;
    }
}
