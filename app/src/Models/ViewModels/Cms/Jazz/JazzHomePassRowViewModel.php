<?php

namespace App\Models\ViewModels\Cms\Jazz;

class JazzHomePassRowViewModel
{
    public int $id;
    public string $label;
    public string $price;
    public bool $highlight;

    public function __construct(int $id, string $label, string $price, bool $highlight)
    {
        $this->id = $id;
        $this->label = $label;
        $this->price = $price;
        $this->highlight = $highlight;
    }
}
