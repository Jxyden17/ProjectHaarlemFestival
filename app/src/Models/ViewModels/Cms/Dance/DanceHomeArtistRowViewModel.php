<?php

namespace App\Models\ViewModels\Cms\Dance;

class DanceHomeArtistRowViewModel
{
    public int $id;
    public string $name;
    public string $genre;
    public string $image;

    public function __construct(int $id, string $name, string $genre, string $image)
    {
        $this->id = $id;
        $this->name = $name;
        $this->genre = $genre;
        $this->image = $image;
    }
}
