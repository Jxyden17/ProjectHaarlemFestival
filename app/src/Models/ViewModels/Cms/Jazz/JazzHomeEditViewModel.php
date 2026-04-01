<?php

namespace App\Models\ViewModels\Cms\Jazz;

class JazzHomeEditViewModel
{
    public string $pageTitle;
    public string $scheduleTitle;
    public string $featuredArtistsTitle;
    public string $passesTitle;
    public array $passes;

    public function __construct(string $pageTitle, string $scheduleTitle, string $featuredArtistsTitle,  string $passesTitle, array $passes) {
        $this->pageTitle = $pageTitle;
        $this->scheduleTitle = $scheduleTitle;
        $this->featuredArtistsTitle = $featuredArtistsTitle;
        $this->passesTitle = $passesTitle;
        $this->passes = $passes;
    }
}
