<?php

namespace App\Models\Edit\Jazz;

class JazzDetailHighlightEditRow
{
    private int $id;
    private string $icon;
    private string $title;
    private string $content;

    private function __construct(int $id, string $icon, string $title, string $content)
    {
        $this->id = $id;
        $this->icon = $icon;
        $this->title = $title;
        $this->content = $content;
    }

    public static function fromArray(array $input): self
    {
        return new self(
            (int)($input['id'] ?? 0),
            trim((string)($input['icon'] ?? '')),
            trim((string)($input['title'] ?? '')),
            trim((string)($input['content'] ?? ''))
        );
    }

    public function id(): int { return $this->id; }
    public function icon(): string { return $this->icon; }
    public function title(): string { return $this->title; }
    public function content(): string { return $this->content; }
}
