<?php

namespace App\Models\Requests\Cms\Dance;

class DanceDetailTrackRowRequest
{
    private int $id;
    private string $title;
    private string $subtitle;
    private string $year;
    private string $image;

    private function __construct(int $id, string $title, string $subtitle, string $year, string $image)
    {
        $this->id = $id;
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->year = $year;
        $this->image = $image;
    }

    public static function fromArray(array $input): self
    {
        return new self(
            (int)($input['id'] ?? 0),
            trim((string)($input['title'] ?? '')),
            trim((string)($input['subtitle'] ?? '')),
            trim((string)($input['year'] ?? '')),
            trim((string)($input['image'] ?? ''))
        );
    }

    public function id(): int { return $this->id; }
    public function title(): string { return $this->title; }
    public function subtitle(): string { return $this->subtitle; }
    public function year(): string { return $this->year; }
    public function image(): string { return $this->image; }
}
