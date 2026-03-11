<?php

namespace App\Models\ViewModels\Cms\Dance;

class DanceDetailTrackRowViewModel
{
    public int $id;
    public string $title;
    public string $subtitle;
    public string $year;
    public string $image;
    public string $audioUrl;

    public function __construct(int $id, string $title, string $subtitle, string $year, string $image, string $audioUrl = '')
    {
        $this->id = $id;
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->year = $year;
        $this->image = $image;
        $this->audioUrl = $audioUrl;
    }
}
