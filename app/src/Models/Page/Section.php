<?php

namespace App\Models\Page;

class Section
{
    public int $id;
    public string $type;
    public string $title;
    public string $description;
    public string $duration;

    public array $items = [];

    public function __construct(int $id, string $type, string $title)
    {
        $this->id = $id;
        $this->type = $type;
        $this->title = $title;
    }
}