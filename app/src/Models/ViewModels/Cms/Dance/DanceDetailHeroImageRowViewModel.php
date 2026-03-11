<?php

namespace App\Models\ViewModels\Cms\Dance;

class DanceDetailHeroImageRowViewModel
{
    public int $id;
    public string $image;
    public string $alt;

    public function __construct(int $id, string $image, string $alt)
    {
        $this->id = $id;
        $this->image = $image;
        $this->alt = $alt;
    }
}
