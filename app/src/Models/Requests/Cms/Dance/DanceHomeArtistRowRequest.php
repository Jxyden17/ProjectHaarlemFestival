<?php

namespace App\Models\Requests\Cms\Dance;

class DanceHomeArtistRowRequest
{
    private int $id;
    private string $name;
    private string $genre;
    private string $image;

    public function __construct(int $id, string $name, string $genre, string $image)
    {
        $this->id = $id;
        $this->name = $name;
        $this->genre = $genre;
        $this->image = $image;
    }

    public static function fromArray(array $input): self
    {
        return new self(
            (int)($input['id'] ?? 0),
            trim((string)($input['name'] ?? '')),
            trim((string)($input['genre'] ?? '')),
            trim((string)($input['image'] ?? ''))
        );
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function genre(): string
    {
        return $this->genre;
    }

    public function image(): string
    {
        return $this->image;
    }
}
