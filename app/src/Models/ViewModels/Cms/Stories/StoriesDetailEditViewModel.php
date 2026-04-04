<?php

namespace App\Models\ViewModels\Cms\Stories;

class StoriesDetailEditViewModel
{
    public int $pageId;
    public string $editorTitle;
    public string $pageSlug;
    public string $publicPath;
    public StoriesSectionEditViewModel $hero;
    public array $heroImageItems;
    public array $heroTagItems;
    public StoriesSectionEditViewModel $about;
    public array $aboutItems;
    public StoriesSectionEditViewModel $gallery;
    public array $galleryItems;
    public StoriesSectionEditViewModel $featured;
    public array $featuredItems;
    public StoriesSectionEditViewModel $booking;
    public array $bookingButtonItems;
    public array $bookingPriceItems;
    public array $bookingPriceLabelItems;
    public array $bookingDateItems;
    public array $bookingLocationItems;
    public array $bookingTagItems;

    public function __construct(
        int $pageId,
        string $editorTitle,
        string $pageSlug,
        string $publicPath,
        StoriesSectionEditViewModel $hero,
        array $heroImageItems,
        array $heroTagItems,
        StoriesSectionEditViewModel $about,
        array $aboutItems,
        StoriesSectionEditViewModel $gallery,
        array $galleryItems,
        StoriesSectionEditViewModel $featured,
        array $featuredItems,
        StoriesSectionEditViewModel $booking,
        array $bookingButtonItems,
        array $bookingPriceItems,
        array $bookingPriceLabelItems,
        array $bookingDateItems,
        array $bookingLocationItems,
        array $bookingTagItems
    ) {
        $this->pageId = $pageId;
        $this->editorTitle = $editorTitle;
        $this->pageSlug = $pageSlug;
        $this->publicPath = $publicPath;
        $this->hero = $hero;
        $this->heroImageItems = $heroImageItems;
        $this->heroTagItems = $heroTagItems;
        $this->about = $about;
        $this->aboutItems = $aboutItems;
        $this->gallery = $gallery;
        $this->galleryItems = $galleryItems;
        $this->featured = $featured;
        $this->featuredItems = $featuredItems;
        $this->booking = $booking;
        $this->bookingButtonItems = $bookingButtonItems;
        $this->bookingPriceItems = $bookingPriceItems;
        $this->bookingPriceLabelItems = $bookingPriceLabelItems;
        $this->bookingDateItems = $bookingDateItems;
        $this->bookingLocationItems = $bookingLocationItems;
        $this->bookingTagItems = $bookingTagItems;
    }
}
