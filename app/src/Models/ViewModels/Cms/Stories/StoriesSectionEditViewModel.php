<?php

namespace App\Models\ViewModels\Cms\Stories;

class StoriesSectionEditViewModel
{
    public string $title;
    public string $subTitle;
    public string $description;

    public function __construct(string $title, string $subTitle, string $description)
    {
        $this->title = $title;
        $this->subTitle = $subTitle;
        $this->description = $description;
    }
}
