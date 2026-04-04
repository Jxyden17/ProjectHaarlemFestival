<?php

namespace App\Mapper;

use App\Models\Page\Page;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use App\Models\ViewModels\Cms\Stories\StoriesDetailEditViewModel;
use App\Models\ViewModels\Cms\Stories\StoriesHomeEditViewModel;
use App\Models\ViewModels\Cms\Stories\StoriesItemRowViewModel;
use App\Models\ViewModels\Cms\Stories\StoriesSectionEditViewModel;

class CmsStoriesViewModelMapper
{
    public function mapHomePageToEditViewModel(Page $page): StoriesHomeEditViewModel
    {
        $hero = $page->getSection('hero');
        $grid = $page->getSection('grid');
        $venues = $page->getSection('venues');
        $schedule = $page->getSection('schedule');
        $explore = $page->getSection('explore');
        $faq = $page->getSection('faq');

        return new StoriesHomeEditViewModel(
            'Stories Home Content',
            'stories',
            '/stories',
            $this->mapSection($hero),
            $this->mapItems($hero),
            $this->mapSection($grid),
            $this->mapItems($grid),
            $this->mapSection($venues),
            $this->mapItems($venues),
            $this->mapSection($schedule),
            $this->mapItems($schedule),
            $this->mapSection($explore),
            $this->mapItems($explore),
            $this->mapSection($faq),
            $this->mapItems($faq)
        );
    }

    public function mapDetailPageToEditViewModel(Page $page, int $pageId): StoriesDetailEditViewModel
    {
        $hero = $page->getSection('hero');
        $about = $page->getSection('about');
        $gallery = $page->getSection('gallery');
        $featured = $page->getSection('featured');
        $booking = $page->getSection('booking');

        return new StoriesDetailEditViewModel(
            $pageId,
            $page->title !== '' ? $page->title : 'Stories Detail Content',
            (string) $page->slug,
            '/stories/' . ltrim((string) $page->slug, '/'),
            $this->mapSection($hero),
            $this->mapItemsByCategory($hero, 'image'),
            $this->mapItemsByCategory($hero, 'tag'),
            $this->mapSection($about),
            $this->mapItemsByCategory($about, 'paragraph'),
            $this->mapSection($gallery),
            $this->mapItemsByCategory($gallery, 'gallery'),
            $this->mapSection($featured),
            $this->mapItems($featured),
            $this->mapSection($booking),
            $this->mapItemsByCategory($booking, 'button'),
            $this->mapItemsByCategory($booking, 'price'),
            $this->mapItemsByCategory($booking, 'price_label'),
            $this->mapItemsByCategory($booking, 'datetime'),
            $this->mapItemsByCategory($booking, 'location'),
            $this->mapItemsByCategory($booking, 'tag')
        );
    }

    private function mapSection(?Section $section): StoriesSectionEditViewModel
    {
        return new StoriesSectionEditViewModel(
            $section !== null ? trim((string) $section->title) : '',
            $section !== null ? trim((string) $section->subTitle) : '',
            $section !== null ? trim((string) $section->description) : ''
        );
    }

    private function mapItems(?Section $section): array
    {
        if ($section === null) {
            return [];
        }

        $rows = [];
        foreach ($section->items ?? [] as $item) {
            if ($item instanceof SectionItem) {
                $rows[] = $this->mapItem($item);
            }
        }

        return $rows;
    }

    private function mapItemsByCategory(?Section $section, string $category): array
    {
        if ($section === null) {
            return [];
        }

        $rows = [];
        foreach ($section->getItemsByCategorie($category) as $item) {
            if ($item instanceof SectionItem) {
                $rows[] = $this->mapItem($item);
            }
        }

        return $rows;
    }

    private function mapItem(SectionItem $item): StoriesItemRowViewModel
    {
        return new StoriesItemRowViewModel(
            $item->id,
            trim((string) $item->category),
            trim((string) $item->title),
            trim((string) $item->subTitle),
            trim((string) $item->content),
            trim((string) $item->image),
            trim((string) $item->url),
            trim((string) $item->duration),
            trim((string) $item->icon)
        );
    }
}
