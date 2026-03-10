<?php

namespace App\Service\Cms;

use App\Mapper\CmsDanceMapper;
use App\Models\Event\EventDetailPageModel;
use App\Models\Page\Page;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use App\Models\Requests\Cms\DanceDetailContentRequest;
use App\Models\Requests\Cms\DanceHomeContentRequest;
use App\Models\ViewModels\Cms\Dance\DanceDetailContentViewModel;
use App\Models\ViewModels\Cms\Dance\DanceHomeContentViewModel;
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
    private CmsDanceMapper $cmsDanceMapper;

    public function __construct(
        IDanceRepository $danceRepository,
        IPageRepository $pageRepository,
        IPageService $pageService,
        IHtmlSanitizerService $htmlSanitizer,
        CmsDanceMapper $cmsDanceMapper
    )
    {
        $this->danceRepository = $danceRepository;
        $this->pageRepository = $pageRepository;
        $this->pageService = $pageService;
        $this->htmlSanitizer = $htmlSanitizer;
        $this->cmsDanceMapper = $cmsDanceMapper;
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

        $passRows = $this->cmsDanceMapper->mapPassViewModels($passes);

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
            $this->cmsDanceMapper->mapHeroImageViewModels($hero),
            $highlights !== null ? $highlights->title : '',
            $this->cmsDanceMapper->mapHighlightViewModels($highlights),
            $tracks !== null ? $tracks->title : '',
            $tracks !== null ? (string)$tracks->description : '',
            $this->cmsDanceMapper->mapTrackViewModels($tracks),
            $info !== null ? $info->title : '',
            $info !== null ? (string)$info->description : ''
        );
    }

    public function saveDanceDetailPage(string $detailSlug, DanceDetailContentRequest $request): void
    {
        $meta = $this->resolveDetailPageMeta($detailSlug);
        $existingTrackAudioUrls = $this->getExistingTrackAudioUrlsByItemId($meta->pageSlug);
        $normalizedInput = $this->normalizeDetailPageInput($request, $existingTrackAudioUrls);
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
            'pass_items' => $this->cmsDanceMapper->normalizePasses($passes),
            'capacity_title' => $request->capacityTitle(),
            'capacity_html' => $this->sanitizeWysiwygField($request->capacityHtml()),
            'special_title' => $request->specialTitle(),
            'special_html' => $this->sanitizeWysiwygField($request->specialHtml()),
        ];
    }

    private function normalizeDetailPageInput(DanceDetailContentRequest $request, array $existingTrackAudioUrls = []): array
    {
        return [
            'hero_title' => trim($request->heroTitle()),
            'hero_badge' => trim($request->heroBadge()),
            'hero_subtitle' => trim($request->heroSubtitle()),
            'hero_images' => $this->cmsDanceMapper->normalizeHeroImages($request->heroImages()),
            'highlights_title' => trim($request->highlightsTitle()),
            'highlights' => $this->cmsDanceMapper->normalizeHighlights($request->highlights()),
            'tracks_title' => trim($request->tracksTitle()),
            'tracks_note' => trim($request->tracksNote()),
            'tracks' => $this->cmsDanceMapper->normalizeTracks($request->tracks(), $existingTrackAudioUrls),
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

    private function getExistingTrackAudioUrlsByItemId(string $pageSlug): array
    {
        $page = $this->pageService->getPageBySlug($pageSlug, 'Dance Detail Content');
        $tracksSection = $page->getSection('dance_detail_tracks');
        if ($tracksSection === null) {
            return [];
        }

        $urlsByItemId = [];
        foreach ($tracksSection->getItemsByCategorie('track') as $item) {
            if (!$item instanceof SectionItem || $item->id <= 0) {
                continue;
            }

            $urlsByItemId[$item->id] = trim((string)($item->url ?? ''));
        }

        return $urlsByItemId;
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
        $this->pageRepository->saveOrUpdateSectionItems($passesSectionId, $this->cmsDanceMapper->mapPassRows($passesSection->items));

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
        $this->pageRepository->saveOrUpdateSectionItems($heroSectionId, $this->cmsDanceMapper->mapHeroImageRows($heroSection->items));

        $highlightsSectionId = $this->pageRepository->saveOrUpdateSection($pageId, 'dance_detail_highlights', $highlightsSection->title, null, null, 20);
        $this->pageRepository->saveOrUpdateSectionItems($highlightsSectionId, $this->cmsDanceMapper->mapHighlightRows($highlightsSection->items));

        $tracksSectionId = $this->pageRepository->saveOrUpdateSection($pageId, 'dance_detail_tracks', $tracksSection->title, null, $tracksSection->description, 30);
        $this->pageRepository->saveOrUpdateSectionItems($tracksSectionId, $this->cmsDanceMapper->mapTrackRows($tracksSection->items));

        $this->pageRepository->saveOrUpdateSection($pageId, 'dance_detail_info', $infoSection->title, null, $infoSection->description, 40);
    }
}
