<?php

namespace App\Controllers;

use App\Models\Event\EventDetailPageModel;
use App\Models\Event\PerformerModel;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use App\Models\ViewModels\Dance\DanceDetailViewModel;
use App\Models\ViewModels\Dance\DanceIndexViewModel;
use App\Models\ViewModels\Shared\ScheduleViewModel;
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

        if (!$detailMeta instanceof EventDetailPageModel) {
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
        $scheduleSection = $homeContent->getSection('dance_schedule');
        $bannerSection = $homeContent->getSection('dance_banner');
        $artistsSection = $homeContent->getSection('dance_artists');
        $infoSection = $homeContent->getSection('dance_info');
        $passesSection = $homeContent->getSection('dance_passes');
        $capacitySection = $homeContent->getSection('dance_capacity');
        $specialSection = $homeContent->getSection('dance_special_session');

        $scheduleTitle = $scheduleSection === null ? '' : trim((string)$scheduleSection->title);
        $schedule = $this->scheduleService->getScheduleDataForEvent('Dance', $scheduleTitle);
        [$totalEvents, $totalLocations] = $this->extractScheduleStats($schedule);

        $passes = [];
        if ($passesSection !== null) {
            foreach ($passesSection->getItemsByCategorie('pass') as $item) {
                if (!$item instanceof SectionItem) {
                    continue;
                }

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
        }

        return new DanceIndexViewModel(
            $schedule,
            $bannerSection?->subTitle,
            $bannerSection?->title,
            $bannerSection?->description,
            $totalEvents,
            $totalLocations,
            $artistsSection?->title,
            $this->buildArtistCards($artistsSection),
            $infoSection?->title,
            $infoSection?->description,
            $passesSection?->title,
            $passes,
            $capacitySection?->title,
            $capacitySection?->description,
            $specialSection?->title,
            $specialSection?->description,
            $this->danceService->getDanceVenues()
        );
    }

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

    private function buildDetailViewModel(EventDetailPageModel $detailMeta): DanceDetailViewModel
    {
        $detailPage = $this->danceService->getDanceDetailPage($detailMeta->pageSlug);
        $heroSection = $detailPage->getSection('dance_detail_hero');
        $highlightsSection = $detailPage->getSection('dance_detail_highlights');
        $tracksSection = $detailPage->getSection('dance_detail_tracks');
        $infoSection = $detailPage->getSection('dance_detail_info');

        $performerName = trim((string)($detailMeta->performerName ?? ''));
        if ($performerName === '') {
            $performerName = $heroSection === null ? '' : trim((string)$heroSection->title);
        }

        $heroImageItems = [];
        if ($heroSection !== null) {
            $heroImageItems = array_values(array_filter(
                $heroSection->getItemsByCategorie('hero_image'),
                static fn($item) => $item instanceof SectionItem
            ));
        }

        $heroImages = [];
        $heroSlots = [
            [$heroImageItems[0] ?? null, ''],
            [$heroImageItems[1] ?? null, $performerName],
            [$heroImageItems[2] ?? null, ''],
        ];

        foreach ($heroSlots as [$item, $fallbackAlt]) {
            $image = $item instanceof SectionItem ? trim((string)($item->image ?? '')) : '';
            $alt = $item instanceof SectionItem ? trim((string)($item->subTitle ?? '')) : '';
            if ($alt === '') {
                $alt = $fallbackAlt;
            }

            $heroImages[] = [
                'image' => $image,
                'alt' => $alt,
            ];
        }

        $highlightItems = [];
        if ($highlightsSection !== null) {
            foreach ($highlightsSection->getItemsByCategorie('highlight') as $item) {
                if (!$item instanceof SectionItem) {
                    continue;
                }

                $highlightItems[] = [
                    'icon' => trim((string)($item->icon ?? '')) ?: 'star',
                    'title' => $item->title,
                    'content' => trim((string)($item->content ?? '')),
                ];
            }
        }

        $trackItems = [];
        if ($tracksSection !== null) {
            foreach ($tracksSection->getItemsByCategorie('track') as $item) {
                if (!$item instanceof SectionItem) {
                    continue;
                }

                $trackItems[] = [
                    'title' => $item->title,
                    'subtitle' => trim((string)($item->subTitle ?? '')),
                    'year' => trim((string)($item->content ?? '')),
                    'image' => trim((string)($item->image ?? '')),
                ];
            }
        }

        return new DanceDetailViewModel(
            $performerName,
            $heroSection?->subTitle,
            $heroSection?->description,
            $heroImages,
            $highlightsSection?->title,
            $highlightItems,
            $tracksSection?->title,
            $tracksSection?->description,
            $trackItems,
            $this->danceService->getDanceScheduleTitle(),
            $this->getScheduleRowsForDetail($detailMeta),
            $infoSection?->title,
            $infoSection?->description
        );
    }

    private function getScheduleRowsForDetail(EventDetailPageModel $detailMeta): array
    {
        if ($detailMeta->performerId === null) {
            return [];
        }

        return $this->scheduleService->getScheduleRowsByPerformerId('Dance', $detailMeta->performerId);
    }

    private function buildArtistCards(?Section $artistsSection): array
    {
        $detailUrlByPerformerId = $this->getDetailUrlByPerformerId();
        $artistImageRows = [];
        if ($artistsSection !== null) {
            $artistImageRows = array_values(array_filter(
                $artistsSection->getItemsByCategorie('artist'),
                static fn($item) => $item instanceof SectionItem
            ));
        }
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
}
