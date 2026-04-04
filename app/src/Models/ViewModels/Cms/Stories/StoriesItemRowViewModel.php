<?php

namespace App\Models\ViewModels\Cms\Stories;

class StoriesItemRowViewModel
{
    public int $id;
    public string $category;
    public string $title;
    public string $subTitle;
    public string $content;
    public string $image;
    public string $url;
    public string $duration;
    public string $icon;

    public function __construct(
        int $id,
        string $category,
        string $title,
        string $subTitle,
        string $content,
        string $image,
        string $url,
        string $duration,
        string $icon
    ) {
        $this->id = $id;
        $this->category = $category;
        $this->title = $title;
        $this->subTitle = $subTitle;
        $this->content = $content;
        $this->image = $image;
        $this->url = $url;
        $this->duration = $duration;
        $this->icon = $icon;
    }
}
