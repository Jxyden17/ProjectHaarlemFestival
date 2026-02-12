<?php

namespace App\Models\Page;

class SectionItem
{
    public int $id;
    public string $title;
    public ?string $content;
    public ?string $image;
    public ?string $url;
    public string $category;

    public function __construct(int $id, string $title, ?string $content, ?string $image, ?string $url, string $category)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->image = $image;
        $this->url = $url;
        $this->category = $category;
    }

}