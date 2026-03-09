<?php

namespace App\Service\Cms;

use App\Models\Event\EventDetailPageModel;
use App\Models\Page\Page;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use App\Models\Requests\Cms\DanceDetailContentRequest;
use App\Models\Requests\Cms\DanceHomeContentRequest;
use App\Models\Requests\Cms\Dance\DanceDetailHeroImageRowRequest;
use App\Models\Requests\Cms\Dance\DanceDetailHighlightRowRequest;
use App\Models\Requests\Cms\Dance\DanceDetailTrackRowRequest;
use App\Models\Requests\Cms\Dance\DanceHomePassRowRequest;
use App\Models\ViewModels\Cms\Dance\DanceDetailContentViewModel;
use App\Models\ViewModels\Cms\Dance\DanceDetailHeroImageRowViewModel;
use App\Models\ViewModels\Cms\Dance\DanceDetailHighlightRowViewModel;
use App\Models\ViewModels\Cms\Dance\DanceDetailTrackRowViewModel;
use App\Models\ViewModels\Cms\Dance\DanceHomeContentViewModel;
use App\Models\ViewModels\Cms\Dance\DanceHomePassRowViewModel;
use App\Repository\Interfaces\IDanceRepository;
use App\Repository\Interfaces\IPageRepository;
use App\Service\Cms\Interfaces\ICmsDanceService;
use App\Service\Interfaces\IHtmlSanitizerService;
use App\Service\Interfaces\IPageService;

class CmsDanceService implements ICmsDanceService
{
    private IDanceRepository $danceRepository;
    private IPageRepository $pageRepository;
    private IPageService $pageService;
    private IHtmlSanitizerService $htmlSanitizer;

    public function __construct(IDanceRepository $danceRepository, IPageRepository $pageRepository, IPageService $pageService, IHtmlSanitizerService $htmlSanitizer)
    {
        $this->danceRepository = $danceRepository;
        $this->pageRepository = $pageRepository;
        $this->pageService = $pageService;
        $this->htmlSanitizer = $htmlSanitizer;
    }

    public function getDanceHomeFormData(): DanceHomeContentViewModel
    {
        $page = $this->getDanceHomePage();
        $schedule = $page->getSection('dance_schedule');
        $banner = $page->getSection('dance_banner');
        $info = $page->getSection('dance_info');
        $passes = $page->getSection('dance_passes');
        $capacity = $page->getSection('dance_capacity');
        $special = $page->getSection('dance_special_session');

        $passRows = [];
        if ($passes !== null) {
            foreach ($passes->getItemsByCategorie('pass') as $item) {
                if (!$item instanceof SectionItem) {
                    continue;
                }

                $passRows[] = new DanceHomePassRowViewModel(
                    $item->id,
                    $item->title,
                    (string)($item->content ?? ''),
                    (string)($item->url ?? '') === 'highlight'
                );
            }
        }

        return new DanceHomeContentViewModel(
            $schedule !== null ? $schedule->title : '',
            $banner !== null ? (string)$banner->subTitle : '',
            $banner !== null ? $banner->title : '',
            $banner !== null ? (string)$banner->description : '',
            '',
            [],
            $info !== null ? $info->title : '',
            $info !== null ? (string)$info->description : '',
            $passes !== null ? $passes->title : '',
            $passRows,
            $capacity !== null ? $capacity->title : '',
            $capacity !== null ? (string)$capacity->description : '',
            $special !== null ? $special->title : '',
            $special !== null ? (string)$special->description : ''
        );
    }

    public function saveDanceHomePage(DanceHomeContentRequest $request): void
    {
        $normalizedInput = $this->normalizeHomePageInput($request);
        $this->validateHomePageInput($normalizedInput);
        $page = $this->buildDanceHomePage($normalizedInput);
        $this->persistDanceHomePage($page);
    }

    public function getDanceDetailFormData(string $detailSlug): DanceDetailContentViewModel
    {
        $meta = $this->resolveDetailPageMeta($detailSlug);
        $page = $this->pageService->getPageBySlug($meta->pageSlug, $this->buildEditorTitle($meta));
        $hero = $page->getSection('dance_detail_hero');
        $highlights = $page->getSection('dance_detail_highlights');
        $tracks = $page->getSection('dance_detail_tracks');
        $info = $page->getSection('dance_detail_info');

        return new DanceDetailContentViewModel(
            $meta->cmsSlug,
            $this->buildEditorTitle($meta),
            $meta->getPublicPath(),
            (string)($meta->performerName ?? ''),
            $hero !== null ? $hero->title : (string)($meta->performerName ?? ''),
            $hero !== null ? (string)$hero->subTitle : '',
            $hero !== null ? (string)$hero->description : '',
            $this->mapHeroImageViewModels($hero),
            $highlights !== null ? $highlights->title : '',
            $this->mapHighlightViewModels($highlights),
            $tracks !== null ? $tracks->title : '',
            $tracks !== null ? (string)$tracks->description : '',
            $this->mapTrackViewModels($tracks),
            $info !== null ? $info->title : '',
            $info !== null ? (string)$info->description : ''
        );
    }

    public function saveDanceDetailPage(string $detailSlug, DanceDetailContentRequest $request): void
    {
        $meta = $this->resolveDetailPageMeta($detailSlug);
        $normalizedInput = $this->normalizeDetailPageInput($request);
        $this->validateDetailPageInput($normalizedInput);
        $page = $this->buildDanceDetailPage($meta->pageSlug, $this->buildEditorTitle($meta), $normalizedInput);
        $this->persistDanceDetailPage($meta->pageSlug, $page);
    }

    private function getDanceHomePage(): Page
    {
        return $this->pageService->getPageBySlug('dance-home', 'Dance Home');
    }

    private function resolveDetailPageMeta(string $detailSlug): EventDetailPageModel
    {
        $meta = $this->danceRepository->findDetailPageByCmsSlug($detailSlug);
        if ($meta === null) {
            throw new \InvalidArgumentException('Unknown dance detail page.');
        }

        return $meta;
    }

    private function buildEditorTitle(EventDetailPageModel $meta): string
    {
        $performerName = trim((string)($meta->performerName ?? ''));
        if ($performerName === '') {
            return 'Dance Detail Content';
        }

        return $performerName . ' Detail Content';
    }

    private function normalizeHomePageInput(DanceHomeContentRequest $request): array
    {
        $passes = $request->passes();

        return [
            'schedule_title' => $request->scheduleTitle(),
            'banner_badge' => $request->bannerBadge(),
            'banner_title' => $request->bannerTitle(),
            'banner_description' => $this->sanitizeWysiwygField($request->bannerDescription()),
            'important_information_title' => $request->importantInformationTitle(),
            'important_information_html' => $this->sanitizeWysiwygField($request->importantInformationHtml()),
            'passes_title' => $request->passesTitle(),
            'pass_items' => $this->normalizePasses($passes),
            'capacity_title' => $request->capacityTitle(),
            'capacity_html' => $this->sanitizeWysiwygField($request->capacityHtml()),
            'special_title' => $request->specialTitle(),
            'special_html' => $this->sanitizeWysiwygField($request->specialHtml()),
        ];
    }

    private function normalizeDetailPageInput(DanceDetailContentRequest $request): array
    {
        return [
            'hero_title' => trim($request->heroTitle()),
            'hero_badge' => trim($request->heroBadge()),
            'hero_subtitle' => trim($request->heroSubtitle()),
            'hero_images' => $this->normalizeHeroImages($request->heroImages()),
            'highlights_title' => trim($request->highlightsTitle()),
            'highlights' => $this->normalizeHighlights($request->highlights()),
            'tracks_title' => trim($request->tracksTitle()),
            'tracks_note' => trim($request->tracksNote()),
            'tracks' => $this->normalizeTracks($request->tracks()),
            'important_information_title' => trim($request->importantInformationTitle()),
            'important_information_html' => $this->sanitizeWysiwygField($request->importantInformationHtml()),
        ];
    }

    private function sanitizeWysiwygField(string $value): string
    {
        return $this->htmlSanitizer->sanitizeWysiwygHtml($value);
    }

    private function validateHomePageInput(array $input): void
    {
        if ($input['schedule_title'] === '') {
            throw new \InvalidArgumentException('Schedule title is required.');
        }

        if ($input['banner_title'] === '') {
            throw new \InvalidArgumentException('Banner title is required.');
        }

        if ($input['banner_description'] === '') {
            throw new \InvalidArgumentException('Banner description is required.');
        }

        if ($input['important_information_title'] === '' || $input['important_information_html'] === '') {
            throw new \InvalidArgumentException('Important information is required.');
        }

        if ($input['passes_title'] === '' || count($input['pass_items']) === 0) {
            throw new \InvalidArgumentException('At least one pass row is required.');
        }

        if ($input['capacity_title'] === '' || $input['capacity_html'] === '') {
            throw new \InvalidArgumentException('Capacity content is required.');
        }

        if ($input['special_title'] === '' || $input['special_html'] === '') {
            throw new \InvalidArgumentException('Special session content is required.');
        }
    }

    private function validateDetailPageInput(array $input): void
    {
        if ($input['hero_title'] === '') {
            throw new \InvalidArgumentException('Hero title is required.');
        }

        if ($input['hero_subtitle'] === '') {
            throw new \InvalidArgumentException('Hero subtitle is required.');
        }

        if (count($input['hero_images']) === 0) {
            throw new \InvalidArgumentException('At least one hero image row is required.');
        }

        if ($input['highlights_title'] === '' || count($input['highlights']) === 0) {
            throw new \InvalidArgumentException('At least one highlight is required.');
        }

        if ($input['tracks_title'] === '' || count($input['tracks']) === 0) {
            throw new \InvalidArgumentException('At least one track is required.');
        }

        if ($input['important_information_title'] === '' || $input['important_information_html'] === '') {
            throw new \InvalidArgumentException('Important information is required.');
        }
    }

    private function buildDanceHomePage(array $input): Page
    {
        $page = new Page('Dance Home', 'dance-home');
        $page->sections = [
            new Section(0, 'dance_schedule', $input['schedule_title'], '', ''),
            new Section(0, 'dance_banner', $input['banner_title'], $input['banner_badge'], $input['banner_description']),
            new Section(0, 'dance_info', $input['important_information_title'], '', $input['important_information_html']),
            new Section(0, 'dance_passes', $input['passes_title'], '', ''),
            new Section(0, 'dance_capacity', $input['capacity_title'], '', $input['capacity_html']),
            new Section(0, 'dance_special_session', $input['special_title'], '', $input['special_html']),
        ];

        $this->appendSectionItems($page, 'dance_passes', $input['pass_items']);

        return $page;
    }

    private function buildDanceDetailPage(string $pageSlug, string $pageName, array $input): Page
    {
        $page = new Page($pageName, $pageSlug);
        $page->sections = [
            new Section(0, 'dance_detail_hero', $input['hero_title'], $input['hero_badge'], $input['hero_subtitle']),
            new Section(0, 'dance_detail_highlights', $input['highlights_title'], '', ''),
            new Section(0, 'dance_detail_tracks', $input['tracks_title'], '', $input['tracks_note']),
            new Section(0, 'dance_detail_info', $input['important_information_title'], '', $input['important_information_html']),
        ];

        $this->appendSectionItems($page, 'dance_detail_hero', $input['hero_images']);
        $this->appendSectionItems($page, 'dance_detail_highlights', $input['highlights']);
        $this->appendSectionItems($page, 'dance_detail_tracks', $input['tracks']);

        return $page;
    }

    private function appendSectionItems(Page $page, string $sectionType, array $items): void
    {
        $section = $page->getSection($sectionType);
        if ($section === null) {
            return;
        }

        foreach ($items as $item) {
            if ($item instanceof SectionItem) {
                $section->addItem($item);
            }
        }
    }

    private function normalizePasses(array $passes): array
    {
        $result = [];
        foreach ($passes as $pass) {
            if (!$pass instanceof DanceHomePassRowRequest) {
                continue;
            }

            $label = $pass->label();
            $price = $pass->price();
            if ($label === '' || $price === '') {
                continue;
            }

            $result[] = new SectionItem($pass->id(), $label, $price, null, $pass->highlight() ? 'highlight' : null, 'pass', null, null, null, count($result) + 1);
        }

        return $result;
    }

    private function normalizeHeroImages(array $heroImages): array
    {
        $result = [];
        foreach ($heroImages as $image) {
            if (!$image instanceof DanceDetailHeroImageRowRequest) {
                continue;
            }

            $result[] = new SectionItem(
                $image->id(),
                '',
                null,
                $image->image(),
                null,
                'hero_image',
                null,
                null,
                $image->alt(),
                count($result) + 1
            );
        }

        return $result;
    }

    private function normalizeHighlights(array $highlights): array
    {
        $result = [];
        foreach ($highlights as $highlight) {
            if (!$highlight instanceof DanceDetailHighlightRowRequest) {
                continue;
            }

            if ($highlight->title() === '' && $highlight->content() === '') {
                continue;
            }

            $result[] = new SectionItem(
                $highlight->id(),
                $highlight->title(),
                $highlight->content(),
                null,
                null,
                'highlight',
                null,
                $highlight->icon() !== '' ? $highlight->icon() : 'star',
                null,
                count($result) + 1
            );
        }

        return $result;
    }

    private function normalizeTracks(array $tracks): array
    {
        $result = [];
        foreach ($tracks as $track) {
            if (!$track instanceof DanceDetailTrackRowRequest) {
                continue;
            }

            if ($track->title() === '' && $track->subtitle() === '' && $track->year() === '' && $track->image() === '') {
                continue;
            }

            $result[] = new SectionItem(
                $track->id(),
                $track->title(),
                $track->year(),
                $track->image(),
                null,
                'track',
                null,
                null,
                $track->subtitle(),
                count($result) + 1
            );
        }

        return $result;
    }

    private function persistDanceHomePage(Page $page): void
    {
        $pageId = $this->pageRepository->findPageIdBySlug('dance-home');
        if ($pageId === null) {
            throw new \RuntimeException('Dance home page not found.');
        }

        $scheduleSection = $page->getSection('dance_schedule');
        $bannerSection = $page->getSection('dance_banner');
        $infoSection = $page->getSection('dance_info');
        $passesSection = $page->getSection('dance_passes');
        $capacitySection = $page->getSection('dance_capacity');
        $specialSection = $page->getSection('dance_special_session');

        if ($scheduleSection === null || $bannerSection === null || $infoSection === null || $passesSection === null || $capacitySection === null || $specialSection === null) {
            throw new \RuntimeException('Required dance sections are missing.');
        }

        $this->pageRepository->saveOrUpdateSection($pageId, 'dance_schedule', $scheduleSection->title, null, null, 5);
        $this->pageRepository->saveOrUpdateSection($pageId, 'dance_banner', $bannerSection->title, $bannerSection->subTitle, $bannerSection->description, 10);
        $this->pageRepository->saveOrUpdateSection($pageId, 'dance_info', $infoSection->title, null, $infoSection->description, 20);

        $passesSectionId = $this->pageRepository->saveOrUpdateSection($pageId, 'dance_passes', $passesSection->title, null, null, 40);
        $this->pageRepository->upsertSectionItems($passesSectionId, $this->mapPassRows($passesSection->items));

        $this->pageRepository->saveOrUpdateSection($pageId, 'dance_capacity', $capacitySection->title, null, $capacitySection->description, 50);
        $this->pageRepository->saveOrUpdateSection($pageId, 'dance_special_session', $specialSection->title, null, $specialSection->description, 60);
    }

    private function persistDanceDetailPage(string $pageSlug, Page $page): void
    {
        $pageId = $this->pageRepository->findPageIdBySlug($pageSlug);
        if ($pageId === null) {
            throw new \RuntimeException('Dance detail page not found.');
        }

        $heroSection = $page->getSection('dance_detail_hero');
        $highlightsSection = $page->getSection('dance_detail_highlights');
        $tracksSection = $page->getSection('dance_detail_tracks');
        $infoSection = $page->getSection('dance_detail_info');

        if ($heroSection === null || $highlightsSection === null || $tracksSection === null || $infoSection === null) {
            throw new \RuntimeException('Required dance detail sections are missing.');
        }

        $heroSectionId = $this->pageRepository->saveOrUpdateSection($pageId, 'dance_detail_hero', $heroSection->title, $heroSection->subTitle, $heroSection->description, 10);
        $this->pageRepository->upsertSectionItems($heroSectionId, $this->mapHeroImageRows($heroSection->items));

        $highlightsSectionId = $this->pageRepository->saveOrUpdateSection($pageId, 'dance_detail_highlights', $highlightsSection->title, null, null, 20);
        $this->pageRepository->upsertSectionItems($highlightsSectionId, $this->mapHighlightRows($highlightsSection->items));

        $tracksSectionId = $this->pageRepository->saveOrUpdateSection($pageId, 'dance_detail_tracks', $tracksSection->title, null, $tracksSection->description, 30);
        $this->pageRepository->upsertSectionItems($tracksSectionId, $this->mapTrackRows($tracksSection->items));

        $this->pageRepository->saveOrUpdateSection($pageId, 'dance_detail_info', $infoSection->title, null, $infoSection->description, 40);
    }

    private function mapPassRows(array $passes): array
    {
        $rows = [];
        $index = 1;

        foreach ($passes as $pass) {
            if (!$pass instanceof SectionItem) {
                continue;
            }

            $label = trim($pass->title);
            $price = trim((string)($pass->content ?? ''));
            $highlight = ($pass->url ?? '') === 'highlight';
            if ($label === '' || $price === '') {
                continue;
            }

            $rows[] = [
                'id' => $pass->id,
                'title' => $label,
                'item_subtitle' => null,
                'content' => $price,
                'image_path' => null,
                'link_url' => $highlight ? 'highlight' : null,
                'duration' => null,
                'icon_class' => null,
                'order_index' => $index++,
                'item_category' => 'pass',
            ];
        }

        return $rows;
    }

    private function mapHeroImageRows(array $heroImages): array
    {
        $rows = [];
        $index = 1;

        foreach ($heroImages as $image) {
            if (!$image instanceof SectionItem) {
                continue;
            }

            $rows[] = [
                'id' => $image->id,
                'title' => '',
                'item_subtitle' => trim((string)($image->subTitle ?? '')),
                'content' => null,
                'image_path' => trim((string)($image->image ?? '')),
                'link_url' => null,
                'duration' => null,
                'icon_class' => null,
                'order_index' => $index++,
                'item_category' => 'hero_image',
            ];
        }

        return $rows;
    }

    private function mapHighlightRows(array $highlights): array
    {
        $rows = [];
        $index = 1;

        foreach ($highlights as $highlight) {
            if (!$highlight instanceof SectionItem) {
                continue;
            }

            $rows[] = [
                'id' => $highlight->id,
                'title' => trim($highlight->title),
                'item_subtitle' => null,
                'content' => trim((string)($highlight->content ?? '')),
                'image_path' => null,
                'link_url' => null,
                'duration' => null,
                'icon_class' => trim((string)($highlight->icon ?? '')) ?: 'star',
                'order_index' => $index++,
                'item_category' => 'highlight',
            ];
        }

        return $rows;
    }

    private function mapTrackRows(array $tracks): array
    {
        $rows = [];
        $index = 1;

        foreach ($tracks as $track) {
            if (!$track instanceof SectionItem) {
                continue;
            }

            $rows[] = [
                'id' => $track->id,
                'title' => trim($track->title),
                'item_subtitle' => trim((string)($track->subTitle ?? '')),
                'content' => trim((string)($track->content ?? '')),
                'image_path' => trim((string)($track->image ?? '')),
                'link_url' => null,
                'duration' => null,
                'icon_class' => null,
                'order_index' => $index++,
                'item_category' => 'track',
            ];
        }

        return $rows;
    }

    private function mapHeroImageViewModels(?Section $heroSection): array
    {
        if ($heroSection === null) {
            return [];
        }

        $rows = [];
        foreach ($heroSection->getItemsByCategorie('hero_image') as $item) {
            if (!$item instanceof SectionItem) {
                continue;
            }

            $rows[] = new DanceDetailHeroImageRowViewModel(
                $item->id,
                trim((string)($item->image ?? '')),
                trim((string)($item->subTitle ?? ''))
            );
        }

        return $rows;
    }

    private function mapHighlightViewModels(?Section $section): array
    {
        if ($section === null) {
            return [];
        }

        $rows = [];
        foreach ($section->getItemsByCategorie('highlight') as $item) {
            if (!$item instanceof SectionItem) {
                continue;
            }

            $rows[] = new DanceDetailHighlightRowViewModel(
                $item->id,
                trim((string)($item->icon ?? '')),
                trim($item->title),
                trim((string)($item->content ?? ''))
            );
        }

        return $rows;
    }

    private function mapTrackViewModels(?Section $section): array
    {
        if ($section === null) {
            return [];
        }

        $rows = [];
        foreach ($section->getItemsByCategorie('track') as $item) {
            if (!$item instanceof SectionItem) {
                continue;
            }

            $rows[] = new DanceDetailTrackRowViewModel(
                $item->id,
                trim($item->title),
                trim((string)($item->subTitle ?? '')),
                trim((string)($item->content ?? '')),
                trim((string)($item->image ?? ''))
            );
        }

        return $rows;
    }
}
