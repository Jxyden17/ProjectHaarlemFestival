<?php

namespace App\Models\ViewModels\Cms\Stories;

class StoriesHomeEditViewModel
{
    public string $editorTitle;
    public string $pageSlug;
    public string $publicPath;
    public StoriesSectionEditViewModel $hero;
    public array $heroItems;
    public StoriesSectionEditViewModel $grid;
    public array $gridItems;
    public StoriesSectionEditViewModel $venues;
    public array $venueItems;
    public StoriesSectionEditViewModel $schedule;
    public array $scheduleItems;
    public StoriesSectionEditViewModel $explore;
    public array $exploreItems;
    public StoriesSectionEditViewModel $faq;
    public array $faqItems;

    public function __construct(
        string $editorTitle,
        string $pageSlug,
        string $publicPath,
        StoriesSectionEditViewModel $hero,
        array $heroItems,
        StoriesSectionEditViewModel $grid,
        array $gridItems,
        StoriesSectionEditViewModel $venues,
        array $venueItems,
        StoriesSectionEditViewModel $schedule,
        array $scheduleItems,
        StoriesSectionEditViewModel $explore,
        array $exploreItems,
        StoriesSectionEditViewModel $faq,
        array $faqItems
    ) {
        $this->editorTitle = $editorTitle;
        $this->pageSlug = $pageSlug;
        $this->publicPath = $publicPath;
        $this->hero = $hero;
        $this->heroItems = $heroItems;
        $this->grid = $grid;
        $this->gridItems = $gridItems;
        $this->venues = $venues;
        $this->venueItems = $venueItems;
        $this->schedule = $schedule;
        $this->scheduleItems = $scheduleItems;
        $this->explore = $explore;
        $this->exploreItems = $exploreItems;
        $this->faq = $faq;
        $this->faqItems = $faqItems;
    }
}
