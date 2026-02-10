<?php

namespace App\Models\Page;

class SectionItem
{
    public string $title;
    public ?string $content;
    public ?string $image;
    public ?string $url;

    public function __construct(string $title, ?string $content, ?string $image, ?string $url)
    {
        $this->title = $title;
        $this->content = $content;
        $this->image = $image;
        $this->url = $url;
    }
}