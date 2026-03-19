<?php

namespace App\Service\Cms;

use App\Mapper\CmsDanceMapper;
use App\Models\Dance\DanceDetailEditorData;
use App\Models\Event\EventDetailPageModel;
use App\Models\Page\Page;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use App\Models\Requests\UpdateDanceDetailRequest;
use App\Models\Requests\UpdateDanceHomeRequest;
use App\Repository\Interfaces\IDanceRepository;
use App\Service\Cms\Interfaces\ICmsDanceService;
use App\Service\Cms\Interfaces\ICmsPageSaveService;
use App\Service\Interfaces\IHtmlSanitizerService;
use App\Service\Interfaces\IPageService;
use App\Validator\CmsDanceValidator;

class CmsDanceService implements ICmsDanceService
{
    private IDanceRepository $danceRepository;
    private ICmsPageSaveService $pageSaveService;
    private IPageService $pageService;
    private IHtmlSanitizerService $htmlSanitizer;
    private CmsDanceMapper $cmsDanceMapper;
    private CmsDanceValidator $danceValidator;

    public function __construct(IDanceRepository $danceRepository, ICmsPageSaveService $pageSaveService, IPageService $pageService, IHtmlSanitizerService $htmlSanitizer, CmsDanceMapper $cmsDanceMapper, CmsDanceValidator $danceValidator)
    {
        $this->danceRepository = $danceRepository;
        $this->pageSaveService = $pageSaveService;
        $this->pageService = $pageService;
        $this->htmlSanitizer = $htmlSanitizer;
        $this->cmsDanceMapper = $cmsDanceMapper;
        $this->danceValidator = $danceValidator;
    }

    public function getDanceHomePage(): Page
    {
        return $this->pageService->getPageBySlug('dance-home', 'Dance Home');
    }

    public function saveDanceHomePage(UpdateDanceHomeRequest $request): void
    {
        $bannerDescription = $this->sanitizeWysiwygField($request->bannerDescription());
        $importantInformationHtml = $this->sanitizeWysiwygField($request->importantInformationHtml());
        $passItems = $this->cmsDanceMapper->normalizePasses($request->passes());
        $capacityHtml = $this->sanitizeWysiwygField($request->capacityHtml());
        $specialHtml = $this->sanitizeWysiwygField($request->specialHtml());

        $this->danceValidator->validateHomePageInput(
            $request,
            $passItems,
            $bannerDescription,
            $importantInformationHtml,
            $capacityHtml,
            $specialHtml
        );

        $page = $this->buildDanceHomePage(
            $request,
            $passItems,
            $bannerDescription,
            $importantInformationHtml,
            $capacityHtml,
            $specialHtml
        );
        $this->persistDanceHomePage($page);
    }

    public function getDanceDetailEditorData(string $pageSlug): DanceDetailEditorData
    {
        $meta = $this->resolveDetailPageMeta($pageSlug);
        $page = $this->pageService->getPageBySlug($meta->pageSlug, $this->buildEditorTitle($meta));
        $performerName = $this->resolveDetailPerformerName($meta, $page->getSection('dance_detail_hero'));

        return new DanceDetailEditorData(
            $meta,
            $page,
            $this->buildEditorTitle($meta),
            $performerName
        );
    }

    public function saveDanceDetailPage(string $pageSlug, UpdateDanceDetailRequest $request): void
    {
        $meta = $this->resolveDetailPageMeta($pageSlug);
        $existingPage = $this->pageService->getPageBySlug($meta->pageSlug, $this->buildEditorTitle($meta));
        $existingTrackAudioUrls = $this->getExistingTrackAudioUrlsByItemId($existingPage);
        $heroImages = $this->cmsDanceMapper->normalizeHeroImages($request->heroImages());
        $highlights = $this->cmsDanceMapper->normalizeHighlights($request->highlights());
        $tracks = $this->cmsDanceMapper->normalizeTracks($request->tracks(), $existingTrackAudioUrls);
        $importantInformationHtml = $this->sanitizeWysiwygField($request->importantInformationHtml());

        $this->danceValidator->validateDetailPageInput(
            $request,
            $heroImages,
            $highlights,
            $tracks,
            $importantInformationHtml
        );

        $page = $this->buildDanceDetailPage(
            $meta->pageSlug,
            $request,
            $heroImages,
            $highlights,
            $tracks,
            $importantInformationHtml
        );
        $page->id = $existingPage->id;
        $this->persistDanceDetailPage($page);
    }

    private function resolveDetailPageMeta(string $pageSlug): EventDetailPageModel
    {
        $meta = $this->danceRepository->findDetailPageByPageSlug($pageSlug);
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

     private function resolveDetailPerformerName(EventDetailPageModel $meta, ?Section $hero): string
    {
        $performerName = trim((string)($meta->performerName ?? ''));
        if ($performerName !== '') {
            return $performerName;
        }

        return $hero === null ? '' : trim((string)$hero->title);
    }

    private function sanitizeWysiwygField(string $value): string
    {
        return $this->htmlSanitizer->sanitizeWysiwygHtml($value);
    }

    private function buildDanceHomePage(UpdateDanceHomeRequest $request, array $passItems, string $bannerDescription, string $importantInformationHtml, string $capacityHtml, string $specialHtml): Page
    {
        $page = new Page($request->pageTitle(), 'dance-home');
        $page->sections = [
            new Section(0, 'dance_schedule', $request->scheduleTitle(), '', ''),
            new Section(0, 'dance_banner', $request->bannerTitle(), $request->bannerBadge(), $bannerDescription),
            new Section(0, 'dance_artists', $request->featuredArtistsTitle(), '', ''),
            new Section(0, 'dance_info', $request->importantInformationTitle(), '', $importantInformationHtml),
            new Section(0, 'dance_passes', $request->passesTitle(), '', ''),
            new Section(0, 'dance_capacity', $request->capacityTitle(), '', $capacityHtml),
            new Section(0, 'dance_special_session', $request->specialTitle(), '', $specialHtml),
        ];

        $this->appendSectionItems($page, 'dance_passes', $passItems);

        return $page;
    }

    private function buildDanceDetailPage(string $pageSlug, UpdateDanceDetailRequest $request, array $heroImages, array $highlights, array $tracks, string $importantInformationHtml): Page
    {
        $page = new Page($request->pageTitle(), $pageSlug);
        $page->sections = [
            new Section(0, 'dance_detail_hero', $request->heroTitle(), $request->heroBadge(), $request->heroSubtitle()),
            new Section(0, 'dance_detail_highlights', $request->highlightsTitle(), '', ''),
            new Section(0, 'dance_detail_tracks', $request->tracksTitle(), '', $request->tracksNote()),
            new Section(0, 'dance_detail_info', $request->importantInformationTitle(), '', $importantInformationHtml),
        ];

        $this->appendSectionItems($page, 'dance_detail_hero', $heroImages);
        $this->appendSectionItems($page, 'dance_detail_highlights', $highlights);
        $this->appendSectionItems($page, 'dance_detail_tracks', $tracks);

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

    private function getExistingTrackAudioUrlsByItemId(Page $page): array
    {
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
        $this->pageSaveService->savePageContentBySlug(
            'dance-home',
            $page->title,
            $this->cmsDanceMapper->mapHomeSectionsForSave($page),
            'Dance home page not found.'
        );
    }

    private function persistDanceDetailPage(Page $page): void
    {
        if ($page->id <= 0) {
            throw new \RuntimeException('Dance detail page not found.');
        }

        $this->pageSaveService->savePageContent(
            $page->id,
            $page->title,
            $this->cmsDanceMapper->mapDetailSectionsForSave($page)
        );
    }
}
