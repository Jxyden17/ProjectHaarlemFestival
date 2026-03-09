<?php

namespace App\Models\Requests\Cms;

use App\Models\Commands\Cms\Dance\DanceDetailSaveCommand;
use App\Models\Requests\Cms\Dance\DanceDetailHeroImageRowRequest;
use App\Models\Requests\Cms\Dance\DanceDetailHighlightRowRequest;
use App\Models\Requests\Cms\Dance\DanceDetailTrackRowRequest;

class DanceDetailContentRequest
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

    private function __construct(
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

    public static function fromArray(array $input): self
    {
        return new self(
            trim((string)($input['hero_title'] ?? '')),
            trim((string)($input['hero_badge'] ?? '')),
            trim((string)($input['hero_subtitle'] ?? '')),
            self::mapHeroImages(is_array($input['hero_images'] ?? null) ? $input['hero_images'] : []),
            trim((string)($input['highlights_title'] ?? '')),
            self::mapHighlights(is_array($input['highlights'] ?? null) ? $input['highlights'] : []),
            trim((string)($input['tracks_title'] ?? '')),
            trim((string)($input['tracks_note'] ?? '')),
            self::mapTracks(is_array($input['tracks'] ?? null) ? $input['tracks'] : []),
            trim((string)($input['important_information_title'] ?? '')),
            trim((string)($input['important_information_html'] ?? ''))
        );
    }

    private static function mapHeroImages(array $input): array
    {
        $rows = [];
        foreach ($input as $row) {
            if (is_array($row)) {
                $rows[] = DanceDetailHeroImageRowRequest::fromArray($row);
            }
        }

        return $rows;
    }

    private static function mapHighlights(array $input): array
    {
        $rows = [];
        foreach ($input as $row) {
            if (is_array($row)) {
                $rows[] = DanceDetailHighlightRowRequest::fromArray($row);
            }
        }

        return $rows;
    }

    private static function mapTracks(array $input): array
    {
        $rows = [];
        foreach ($input as $row) {
            if (is_array($row)) {
                $rows[] = DanceDetailTrackRowRequest::fromArray($row);
            }
        }

        return $rows;
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

    public function toSaveCommand(): DanceDetailSaveCommand
    {
        return new DanceDetailSaveCommand(
            $this->heroTitle(),
            $this->heroBadge(),
            $this->heroSubtitle(),
            $this->heroImages(),
            $this->highlightsTitle(),
            $this->highlights(),
            $this->tracksTitle(),
            $this->tracksNote(),
            $this->tracks(),
            $this->importantInformationTitle(),
            $this->importantInformationHtml()
        );
    }
}
