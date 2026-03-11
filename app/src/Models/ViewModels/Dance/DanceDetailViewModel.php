<?php

namespace App\Models\ViewModels\Dance;

class DanceDetailViewModel
{
    public string $performerName;
    public string $badge;
    public string $subtitle;
    public array $heroImages;
    public string $highlightsTitle;
    public array $highlights;
    public string $tracksTitle;
    public string $tracksNote;
    public array $tracks;
    public string $scheduleTitle;
    public array $scheduleRows;
    public string $importantInfoTitle;
    public string $importantInfoHtml;

    public function __construct(
        ?string $performerName = null,
        ?string $badge = null,
        ?string $subtitle = null,
        ?array $heroImages = null,
        ?string $highlightsTitle = null,
        ?array $highlights = null,
        ?string $tracksTitle = null,
        ?string $tracksNote = null,
        ?array $tracks = null,
        ?string $scheduleTitle = null,
        ?array $scheduleRows = null,
        ?string $importantInfoTitle = null,
        ?string $importantInfoHtml = null
    ) {
        $this->performerName = trim((string)$performerName);
        $this->badge = trim((string)$badge);
        $this->subtitle = trim((string)$subtitle);
        $this->heroImages = $this->normalizeHeroImages($heroImages ?? []);
        $this->highlightsTitle = $this->normalizeText($highlightsTitle, 'Highlights');
        $this->highlights = $highlights ?? [];
        $this->tracksTitle = $this->normalizeText($tracksTitle, 'Tracks');
        $this->tracksNote = trim((string)$tracksNote);
        $this->tracks = $tracks ?? [];
        $this->scheduleTitle = $this->normalizeText($scheduleTitle, 'DANCE! Festival Schedule');
        $this->scheduleRows = $scheduleRows ?? [];
        $this->importantInfoTitle = $this->normalizeText($importantInfoTitle, 'Important Information');
        $this->importantInfoHtml = (string)$importantInfoHtml;
    }

    private function normalizeHeroImages(array $heroImages): array
    {
        $normalized = [];

        for ($index = 0; $index < 3; $index++) {
            $image = $heroImages[$index] ?? null;
            $normalized[] = [
                'image' => trim((string)($image['image'] ?? '')),
                'alt' => trim((string)($image['alt'] ?? '')),
            ];
        }

        return $normalized;
    }

    private function normalizeText(?string $value, string $fallback): string
    {
        $normalized = trim((string)$value);

        return $normalized === '' ? $fallback : $normalized;
    }
}
