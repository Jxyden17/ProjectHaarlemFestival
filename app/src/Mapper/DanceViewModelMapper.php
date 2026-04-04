<?php

namespace App\Mapper;

use App\Models\Dance\DanceDetailData;
use App\Models\Dance\DanceIndexData;
use App\Models\Event\EventDetailPageModel;
use App\Models\Event\PerformerModel;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use App\Models\ViewModels\Dance\DanceDetailViewModel;
use App\Models\ViewModels\Dance\DanceIndexViewModel;
use App\Models\ViewModels\Shared\ScheduleRowViewModel;
use App\Models\ViewModels\Shared\ScheduleViewModel;

class DanceViewModelMapper
{
    private const INDEX_SECTION_BANNER = 'dance_banner';
    private const INDEX_SECTION_ARTISTS = 'dance_artists';
    private const INDEX_SECTION_INFO = 'dance_info';
    private const INDEX_SECTION_PASSES = 'dance_passes';
    private const INDEX_SECTION_CAPACITY = 'dance_capacity';
    private const INDEX_SECTION_SPECIAL = 'dance_special_session';
    private const DETAIL_SECTION_HERO = 'dance_detail_hero';
    private const DETAIL_SECTION_HIGHLIGHTS = 'dance_detail_highlights';
    private const DETAIL_SECTION_TRACKS = 'dance_detail_tracks';
    private const DETAIL_SECTION_INFO = 'dance_detail_info';
    private const ITEM_CATEGORY_PASS = 'pass';
    private const ITEM_CATEGORY_ARTIST = 'artist';
    private const ITEM_CATEGORY_HERO_IMAGE = 'hero_image';
    private const ITEM_CATEGORY_HIGHLIGHT = 'highlight';
    private const ITEM_CATEGORY_TRACK = 'track';

    // Builds the public dance landing view model so one typed page payload becomes render-ready homepage content.
    public function buildIndexViewModel(DanceIndexData $indexData, ScheduleViewModel $schedule): DanceIndexViewModel
    {
        $homePage = $indexData->homePage;
        $bannerSection = $homePage->getSection(self::INDEX_SECTION_BANNER);
        $featuredArtistsSection = $homePage->getSection(self::INDEX_SECTION_ARTISTS);
        $infoSection = $homePage->getSection(self::INDEX_SECTION_INFO);
        $passesSection = $homePage->getSection(self::INDEX_SECTION_PASSES);
        $capacitySection = $homePage->getSection(self::INDEX_SECTION_CAPACITY);
        $specialSection = $homePage->getSection(self::INDEX_SECTION_SPECIAL);
        $detailUrlsByPerformerId = $this->mapDetailUrlsByPerformerId($indexData->detailPages);
        [$totalEvents, $totalLocations] = $this->extractScheduleStats($schedule);

        return new DanceIndexViewModel(
            $homePage->title,
            $schedule,
            $bannerSection?->subTitle,
            $bannerSection?->title,
            $bannerSection?->description,
            $totalEvents,
            $totalLocations,
            $featuredArtistsSection?->title,
            $this->buildFeaturedArtistCardsFromOrderedImages($featuredArtistsSection, $indexData->performers, $detailUrlsByPerformerId),
            $infoSection?->title,
            $infoSection?->description,
            $passesSection?->title,
            $this->buildPasses($passesSection),
            $capacitySection?->title,
            $capacitySection?->description,
            $specialSection?->title,
            $specialSection?->description,
            $indexData->venues
        );
    }

    // Builds the public dance detail view model so one detail payload becomes render-ready hero, highlights, tracks, and schedule content.
    public function buildDetailViewModel(DanceDetailData $detailData, array $scheduleRows): DanceDetailViewModel
    {
        $contentPage = $detailData->contentPage;
        $heroSection = $contentPage->getSection(self::DETAIL_SECTION_HERO);
        $highlightsSection = $contentPage->getSection(self::DETAIL_SECTION_HIGHLIGHTS);
        $tracksSection = $contentPage->getSection(self::DETAIL_SECTION_TRACKS);
        $infoSection = $contentPage->getSection(self::DETAIL_SECTION_INFO);
        $performerName = $this->resolvePerformerName($detailData->detailMeta, $heroSection);

        return new DanceDetailViewModel(
            $contentPage->title,
            $performerName,
            $heroSection?->subTitle,
            $heroSection?->description,
            $this->buildHeroImages($heroSection, $performerName),
            $highlightsSection?->title,
            $this->buildHighlightItems($highlightsSection),
            $tracksSection?->title,
            $tracksSection?->description,
            $this->buildTrackItems($tracksSection),
            '',
            array_values(array_filter($scheduleRows, static fn($row) => $row instanceof ScheduleRowViewModel)),
            $infoSection?->title,
            $infoSection?->description
        );
    }

    // Builds the public pass cards so dance home pricing rows only include complete label and price pairs.
    private function buildPasses(?Section $passesSection): array
    {
        $passes = [];

        foreach ($this->getSectionItemsByCategory($passesSection, self::ITEM_CATEGORY_PASS) as $item) {
            $label = trim($item->title);
            $price = trim((string)($item->content ?? ''));
            if ($label === '' || $price === '') {
                continue;
            }

            $passes[] = [
                'label' => $label,
                'price' => $price,
                'highlight' => (string)($item->url ?? '') === 'highlight',
            ];
        }

        return $passes;
    }

    // Builds the left, center, and right hero image slots so detail pages always receive a fixed hero layout.
    private function buildHeroImages(?Section $heroSection, string $performerName): array
    {
        $heroImageItems = $this->getSectionItemsByCategory($heroSection, self::ITEM_CATEGORY_HERO_IMAGE);
        return [
            'left' => $this->mapHeroImageSlot($heroImageItems[0] ?? null, ''),
            'center' => $this->mapHeroImageSlot($heroImageItems[1] ?? null, $performerName),
            'right' => $this->mapHeroImageSlot($heroImageItems[2] ?? null, ''),
        ];
    }

    // Builds highlight card data so the detail page can render icon, title, and copy from stored section items.
    private function buildHighlightItems(?Section $highlightsSection): array
    {
        $highlightItems = [];

        foreach ($this->getSectionItemsByCategory($highlightsSection, self::ITEM_CATEGORY_HIGHLIGHT) as $item) {
            $highlightItems[] = [
                'icon' => trim((string)($item->icon ?? '')) ?: 'star',
                'title' => $item->title,
                'content' => trim((string)($item->content ?? '')),
            ];
        }

        return $highlightItems;
    }

    // Builds track card data so the detail page can render title, subtitle, year, image, and audio together.
    private function buildTrackItems(?Section $tracksSection): array
    {
        $trackItems = [];

        foreach ($this->getSectionItemsByCategory($tracksSection, self::ITEM_CATEGORY_TRACK) as $item) {
            $trackItems[] = [
                'title' => $item->title,
                'subtitle' => trim((string)($item->subTitle ?? '')),
                'year' => trim((string)($item->content ?? '')),
                'image' => trim((string)($item->image ?? '')),
                'audioUrl' => trim((string)($item->url ?? '')),
            ];
        }

        return $trackItems;
    }

    // Resolves the display performer name so detail pages fall back to the hero title when metadata is blank.
    private function resolvePerformerName(EventDetailPageModel $detailMeta, ?Section $heroSection): string
    {
        $performerName = trim((string)($detailMeta->performerName ?? ''));
        if ($performerName !== '') {
            return $performerName;
        }

        return $heroSection === null ? '' : trim((string)$heroSection->title);
    }

    // Counts total events and unique locations so the dance landing page can show schedule summary stats.
    private function extractScheduleStats(ScheduleViewModel $schedule): array
    {
        $totalEvents = 0;
        $locations = [];

        foreach ($schedule->groups as $group) {
            if (!isset($group->rows) || !is_array($group->rows)) {
                continue;
            }

            foreach ($group->rows as $row) {
                $totalEvents++;
                $location = trim((string)($row->location ?? ''));
                if ($location === '') {
                    continue;
                }

                $locations[$location] = true;
            }
        }

        return [$totalEvents, count($locations)];
    }

    // Builds featured artist cards by matching performer order to stored artist images so the dance home page stays visually aligned.
    private function buildFeaturedArtistCardsFromOrderedImages(?Section $featuredArtistsSection, array $performers, array $detailUrlsByPerformerId): array
    {
        $featuredArtistImageItems = $this->getSectionItemsByCategory($featuredArtistsSection, self::ITEM_CATEGORY_ARTIST);
        $featuredArtistCards = [];

        foreach ($performers as $index => $performer) {
            if (!$performer instanceof PerformerModel) {
                continue;
            }

            $featuredArtistCard = $this->mapFeaturedArtistCard($performer, $featuredArtistImageItems[$index] ?? null, $detailUrlsByPerformerId);
            if ($featuredArtistCard === null) {
                continue;
            }

            $featuredArtistCards[] = $featuredArtistCard;
        }

        return $featuredArtistCards;
    }

    // Maps performer ids to detail URLs so featured artist cards can link directly to matching dance detail pages.
    private function mapDetailUrlsByPerformerId(array $detailPages): array
    {
        $detailUrlsByPerformerId = [];

        foreach ($detailPages as $detailPage) {
            if (!$detailPage instanceof EventDetailPageModel || $detailPage->performerId === null) {
                continue;
            }

            $detailUrlsByPerformerId[$detailPage->performerId] = $detailPage->getPublicPath();
        }

        return $detailUrlsByPerformerId;
    }

    // Builds one featured artist card so performers without a name or image are skipped instead of rendering broken cards.
    private function mapFeaturedArtistCard(PerformerModel $performer, ?SectionItem $featuredArtistImageItem, array $detailUrlsByPerformerId): ?array
    {
        $name = trim($performer->performerName);
        if ($name === '') {
            return null;
        }

        $image = $featuredArtistImageItem instanceof SectionItem ? trim((string)($featuredArtistImageItem->image ?? '')) : '';
        if ($image === '') {
            return null;
        }

        $genre = trim((string)($performer->description ?? ''));
        if ($genre === '') {
            $genre = 'DJ';
        }

        return [
            'name' => $name,
            'genre' => $genre,
            'image' => $image,
            'detailUrl' => $detailUrlsByPerformerId[$performer->id] ?? '',
        ];
    }

    // Builds one hero image slot so the detail page receives a consistent image-plus-alt structure even when a slot is empty.
    private function mapHeroImageSlot(?SectionItem $heroImageItem, string $fallbackAlt): array
    {
        $image = $heroImageItem instanceof SectionItem ? trim((string)($heroImageItem->image ?? '')) : '';
        $alt = $heroImageItem instanceof SectionItem ? trim((string)($heroImageItem->subTitle ?? '')) : '';

        if ($alt === '') {
            $alt = $fallbackAlt;
        }

        return [
            'image' => $image,
            'alt' => $alt,
        ];
    }

    // Returns typed section items for one category so dance view building can ignore unrelated or malformed items.
    private function getSectionItemsByCategory(?Section $section, string $category): array
    {
        if ($section === null) {
            return [];
        }

        return array_values(array_filter(
            $section->getItemsByCategorie($category),
            static fn($item) => $item instanceof SectionItem
        ));
    }
}
