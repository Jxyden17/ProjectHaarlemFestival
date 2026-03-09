<?php

namespace App\Controllers;

use App\Models\Event\EventDetailPageModel;
use App\Models\Event\PerformerModel;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use App\Models\ViewModels\Dance\DanceDetailViewModel;
use App\Models\ViewModels\Dance\DanceIndexViewModel;
use App\Service\Interfaces\IDanceService;
use App\Service\Interfaces\IScheduleService;

class DanceController extends BaseController
{
    private IDanceService $danceService;
    private IScheduleService $scheduleService;

    public function __construct(IDanceService $danceService, IScheduleService $scheduleService)
    {
        $this->danceService = $danceService;
        $this->scheduleService = $scheduleService;
    }

    public function index(): void
    {
        $this->render('dance/index', [
            'title' => 'Dance',
            'danceIndexViewModel' => $this->buildIndexViewModel(),
        ]);
    }

    public function detail(array $vars = []): void
    {
        $publicSlug = trim((string)($vars['detailSlug'] ?? ''));
        $detailMeta = $this->danceService->getDanceDetailPageByPublicSlug($publicSlug);

        if (!$detailMeta instanceof EventDetailPageModel || !$detailMeta->isPublished) {
            http_response_code(404);
            echo 'Dance detail page not found.';
            return;
        }

        $detailViewModel = $this->buildDetailViewModel($detailMeta);

        $this->render('dance/detail', [
            'title' => $detailViewModel->performerName === '' ? 'Dance Detail' : $detailViewModel->performerName,
            'danceDetailViewModel' => $detailViewModel,
        ]);
    }

    private function buildIndexViewModel(): DanceIndexViewModel
    {
        $homeContent = $this->danceService->getDanceHomePage();
        $bannerStats = $this->danceService->getDanceBannerStats();
        $sections = $this->getIndexSections($homeContent);
        $scheduleTitle = $this->getSectionTitle($sections['schedule']);

        return new DanceIndexViewModel(
            $this->scheduleService->getScheduleDataForEvent('Dance', $scheduleTitle),
            $sections['banner']?->subTitle,
            $sections['banner']?->title,
            $sections['banner']?->description,
            $bannerStats->totalEvents,
            $bannerStats->totalLocations,
            $sections['artists']?->title,
            $this->buildArtistCards($sections['artists']),
            $sections['info']?->title,
            $sections['info']?->description,
            $sections['passes']?->title,
            $this->buildPasses($sections['passes']),
            $sections['capacity']?->title,
            $sections['capacity']?->description,
            $sections['special']?->title,
            $sections['special']?->description,
            $this->danceService->getDanceVenues()
        );
    }

    private function buildDetailViewModel(EventDetailPageModel $detailMeta): DanceDetailViewModel
    {
        $detailPage = $this->danceService->getDanceDetailPage($detailMeta->pageSlug);
        $sections = $this->getDetailSections($detailPage);
        $performerName = $this->resolvePerformerName($detailMeta, $sections['hero']);
        $heroImages = $this->getSectionItemsByCategory($sections['hero'], 'hero_image');

        return new DanceDetailViewModel(
            $performerName,
            $sections['hero']?->subTitle,
            $sections['hero']?->description,
            $this->buildHeroImages($heroImages, $performerName),
            $sections['highlights']?->title,
            $this->buildHighlightItems($sections['highlights']),
            $sections['tracks']?->title,
            $sections['tracks']?->description,
            $this->buildTrackItems($sections['tracks']),
            $this->danceService->getDanceScheduleTitle(),
            $this->getScheduleRowsForDetail($detailMeta),
            $sections['info']?->title,
            $sections['info']?->description
        );
    }

    private function getIndexSections($page): array
    {
        return [
            'schedule' => $page->getSection('dance_schedule'),
            'banner' => $page->getSection('dance_banner'),
            'artists' => $page->getSection('dance_artists'),
            'info' => $page->getSection('dance_info'),
            'passes' => $page->getSection('dance_passes'),
            'capacity' => $page->getSection('dance_capacity'),
            'special' => $page->getSection('dance_special_session'),
        ];
    }

    private function getDetailSections($page): array
    {
        return [
            'hero' => $page->getSection('dance_detail_hero'),
            'highlights' => $page->getSection('dance_detail_highlights'),
            'tracks' => $page->getSection('dance_detail_tracks'),
            'info' => $page->getSection('dance_detail_info'),
        ];
    }

    private function getSectionTitle(?Section $section): string
    {
        return $section === null ? '' : trim((string)$section->title);
    }

    private function resolvePerformerName(EventDetailPageModel $detailMeta, ?Section $heroSection): string
    {
        $performerName = trim((string)($detailMeta->performerName ?? ''));
        if ($performerName !== '') {
            return $performerName;
        }

        return $this->getSectionTitle($heroSection);
    }

    private function getScheduleRowsForDetail(EventDetailPageModel $detailMeta): array
    {
        if ($detailMeta->performerId === null) {
            return [];
        }

        return $this->scheduleService->getScheduleRowsByPerformerId('Dance', $detailMeta->performerId);
    }

    private function buildHeroImages(array $heroImages, string $performerName): array
    {
        return [
            $this->mapHeroImage($heroImages[0] ?? null, ''),
            $this->mapHeroImage($heroImages[1] ?? null, $performerName),
            $this->mapHeroImage($heroImages[2] ?? null, ''),
        ];
    }

    private function getSectionItemsByCategory(?Section $section, string $category): array
    {
        if (!$section instanceof Section) {
            return [];
        }

        return array_values(array_filter(
            $section->getItemsByCategorie($category),
            static fn($item) => $item instanceof SectionItem
        ));
    }

    private function buildArtistCards(?Section $artistsSection): array
    {
        $detailUrlByPerformerId = $this->getDetailUrlByPerformerId();
        $artistImageRows = $this->getSectionItemsByCategory($artistsSection, 'artist');
        $artistCards = [];

        foreach ($this->danceService->getDancePerformers() as $index => $performer) {
            if (!$performer instanceof PerformerModel) {
                continue;
            }

            $artistCard = $this->mapArtistCard($performer, $artistImageRows[$index] ?? null, $detailUrlByPerformerId);
            if ($artistCard === null) {
                continue;
            }

            $artistCards[] = $artistCard;
        }

        return $artistCards;
    }

    private function getDetailUrlByPerformerId(): array
    {
        $detailUrlByPerformerId = [];
        foreach ($this->danceService->getPublishedDanceDetailPages() as $detailPage) {
            if ($detailPage instanceof EventDetailPageModel && $detailPage->performerId !== null) {
                $detailUrlByPerformerId[$detailPage->performerId] = $detailPage->getPublicPath();
            }
        }

        return $detailUrlByPerformerId;
    }

    private function mapArtistCard(PerformerModel $performer, ?SectionItem $imageRow, array $detailUrlByPerformerId): ?array
    {
        $name = trim($performer->performerName);
        if ($name === '') {
            return null;
        }

        $image = $imageRow instanceof SectionItem ? trim((string)($imageRow->image ?? '')) : '';
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
            'detailUrl' => $detailUrlByPerformerId[$performer->id] ?? '',
        ];
    }

    private function buildPasses(?Section $passesSection): array
    {
        $passes = [];
        foreach ($this->getSectionItemsByCategory($passesSection, 'pass') as $item) {
            $pass = $this->mapPass($item);
            if ($pass === null) {
                continue;
            }

            $passes[] = $pass;
        }

        return $passes;
    }

    private function mapPass(SectionItem $item): ?array
    {
        $label = trim($item->title);
        $price = trim((string)($item->content ?? ''));
        if ($label === '' || $price === '') {
            return null;
        }

        return [
            'label' => $label,
            'price' => $price,
            'highlight' => (string)($item->url ?? '') === 'highlight',
        ];
    }

    private function buildHighlightItems(?Section $highlightsSection): array
    {
        $highlightItems = [];
        foreach ($this->getSectionItemsByCategory($highlightsSection, 'highlight') as $item) {
            $highlightItems[] = $this->mapHighlightItem($item);
        }

        return $highlightItems;
    }

    private function mapHighlightItem(SectionItem $item): array
    {
        return [
            'icon' => trim((string)($item->icon ?? '')) ?: 'star',
            'title' => $item->title,
            'content' => trim((string)($item->content ?? '')),
        ];
    }

    private function buildTrackItems(?Section $tracksSection): array
    {
        $trackItems = [];
        foreach ($this->getSectionItemsByCategory($tracksSection, 'track') as $item) {
            $trackItems[] = $this->mapTrackItem($item);
        }

        return $trackItems;
    }

    private function mapTrackItem(SectionItem $item): array
    {
        return [
            'title' => $item->title,
            'subtitle' => trim((string)($item->subTitle ?? '')),
            'year' => trim((string)($item->content ?? '')),
            'image' => trim((string)($item->image ?? '')),
        ];
    }

    private function mapHeroImage(?SectionItem $item, string $fallbackAlt): array
    {
        $image = $item instanceof SectionItem ? trim((string)($item->image ?? '')) : '';
        $alt = $item instanceof SectionItem ? trim((string)($item->subTitle ?? '')) : '';
        if ($alt === '') {
            $alt = $fallbackAlt;
        }

        return [
            'image' => $image,
            'alt' => $alt,
        ];
    }
}
