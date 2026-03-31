<?php

namespace App\Models\Edit\Jazz;

class JazzHomePassEditRow
{
    private int $id;
    private string $label;
    private string $price;
    private bool $highlight;

    public function __construct(int $id, string $label, string $price, bool $highlight)
    {
        $this->id = $id;
        $this->label = $label;
        $this->price = $price;
        $this->highlight = $highlight;
    }

    public static function fromArray(array $input): self
    {
        return new self(
            (int)($input['id'] ?? 0),
            trim((string)($input['label'] ?? '')),
            trim((string)($input['price'] ?? '')),
            !empty($input['highlight'])
        );
    }

    public function id(): int
    {
        return $this->id;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function price(): string
    {
        return $this->price;
    }

    public function highlight(): bool
    {
        return $this->highlight;
    }
}
