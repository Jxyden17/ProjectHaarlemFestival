<?php

namespace App\Models\Page;

use App\Models\Page\Section;

class Page
{
    public int $id;
    public string $title;
    public string $slug;
    public array $sections = [];

    public function __construct(string $title, string $slug, int $id = 0)
    {
        $this->id = $id;
        $this->title = $title;
        $this->slug = $slug;
    }

    public function getSection(string $type): ?Section
    {
        foreach ($this->sections as $s) {
            if ($s->type === $type) {
                return $s;
            }
        }
        return null;
    }
}
