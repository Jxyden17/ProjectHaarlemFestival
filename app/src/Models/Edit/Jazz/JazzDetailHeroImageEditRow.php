<?php

namespace App\Models\Edit\Jazz;

class JazzDetailHeroImageEditRow
{
    private int $id;
    private string $image;
    private string $alt;

    private function __construct(int $id, string $image, string $alt)
    {
        $this->id = $id;
        $this->image = $image;
        $this->alt = $alt;
    }

    public static function fromArray(array $input): self
    {
        return new self(
            (int)($input['id'] ?? 0),
            trim((string)($input['image'] ?? '')),
            trim((string)($input['alt'] ?? ''))
        );
    }

    public function id(): int { return $this->id; }
    public function image(): string { return $this->image; }
    public function alt(): string { return $this->alt; }
}
