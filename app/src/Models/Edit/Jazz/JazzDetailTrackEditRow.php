<?php

namespace App\Models\Edit\Jazz;

class JazzDetailTrackEditRow
{
    private int $id;
    private string $title;
    private string $subtitle;
    private string $year;
    private string $image;
    private string $audioUrl;

    private function __construct(int $id, string $title, string $subtitle, string $year, string $image, string $audioUrl)
    {
        $this->id = $id;
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->year = $year;
        $this->image = $image;
        $this->audioUrl = $audioUrl;
    }

    public static function fromArray(array $input): self
    {
        return new self(
            (int)($input['id'] ?? 0),
            trim((string)($input['title'] ?? '')),
            trim((string)($input['subtitle'] ?? '')),
            trim((string)($input['year'] ?? '')),
            trim((string)($input['image'] ?? '')),
            trim((string)($input['audio_url'] ?? ''))
        );
    }

    public function id(): int { return $this->id; }
    public function title(): string { return $this->title; }
    public function subtitle(): string { return $this->subtitle; }
    public function year(): string { return $this->year; }
    public function image(): string { return $this->image; }
    public function audioUrl(): string { return $this->audioUrl; }
}
