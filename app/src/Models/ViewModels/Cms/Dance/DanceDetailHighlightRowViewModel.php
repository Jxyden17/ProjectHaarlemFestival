<?php

namespace App\Models\ViewModels\Cms\Dance;

class DanceDetailHighlightRowViewModel
{
    public int $id;
    public string $icon;
    public string $title;
    public string $content;

    public function __construct(int $id, string $icon, string $title, string $content)
    {
        $this->id = $id;
        $this->icon = $icon;
        $this->title = $title;
        $this->content = $content;
    }
}
