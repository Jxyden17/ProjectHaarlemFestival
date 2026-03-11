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
use App\Service\Cms\Interfaces\ICmsDanceService;
use App\Service\Cms\Interfaces\ICmsPageSaveService;
use App\Service\Interfaces\IHtmlSanitizerService;
use App\Service\Interfaces\IPageService;
use App\Validator\Cms\CmsDanceValidator;

class CmsDanceService implements ICmsDanceService
{
    private IDanceRepository $danceRepository;
    private ICmsPageSaveService $pageSaveService;
    private IPageService $pageService;
    private IHtmlSanitizerService $htmlSanitizer;
    private CmsDanceMapper $cmsDanceMapper;
    private CmsDanceValidator $danceValidator;

    public function __construct(
        IDanceRepository $danceRepository,
        ICmsPageSaveService $pageSaveService,
        IPageService $pageService,
        IHtmlSanitizerService $htmlSanitizer,
        CmsDanceMapper $cmsDanceMapper,
        CmsDanceValidator $danceValidator
    )
    {
        $this->danceRepository = $danceRepository;
        $this->pageSaveService = $pageSaveService;
        $this->pageService = $pageService;
        $this->htmlSanitizer = $htmlSanitizer;
        $this->cmsDanceMapper = $cmsDanceMapper;
        $this->danceValidator = $danceValidator;
    }

    public function getDanceHomeFormData(): DanceHomeContentViewModel
    {
        $page = $this->getDanceHomePage();
        return $this->cmsDanceMapper->mapHomeContentViewModelFromPage($page);
    }

    public function saveDanceHomePage(DanceHomeContentRequest $request): void
    {
        $normalizedInput = $this->normalizeHomePageInput($request);
        $this->danceValidator->validateHomePageInput($normalizedInput);
        $page = $this->buildDanceHomePage($normalizedInput);
        $this->persistDanceHomePage($page);
    }

    public function getDanceDetailFormData(string $detailSlug): DanceDetailContentViewModel
    {
        $meta = $this->resolveDetailPageMeta($detailSlug);
        $page = $this->pageService->getPageBySlug($meta->pageSlug, $this->buildEditorTitle($meta));
        $performerName = trim((string)($meta->performerName ?? ''));

        return $this->cmsDanceMapper->mapDetailContentViewModel(
            $meta,
            $page,
            $this->buildEditorTitle($meta),
            $performerName
        );
    }

    public function saveDanceDetailPage(string $detailSlug, DanceDetailContentRequest $request): void
    {
        $meta = $this->resolveDetailPageMeta($detailSlug);
        $existingPage = $this->pageService->getPageBySlug($meta->pageSlug, $this->buildEditorTitle($meta));
        $existingTrackAudioUrls = $this->getExistingTrackAudioUrlsByItemId($existingPage);
        $normalizedInput = $this->normalizeDetailPageInput($request, $existingTrackAudioUrls);
        $this->danceValidator->validateDetailPageInput($normalizedInput);
        $page = $this->buildDanceDetailPage($meta->pageSlug, $normalizedInput);
        $page->id = $existingPage->id;
        $this->persistDanceDetailPage($page);
    }

    private function getDanceHomePage(): Page
    {
        return $this->pageService->getPageBySlug('dance-home', 'Dance Home');
    }

    private function resolveDetailPageMeta(string $detailSlug): EventDetailPageModel
    {
        $meta = $this->danceRepository->findDetailPageBySlug($detailSlug);
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
            'page_title' => $request->pageTitle(),
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
            'page_title' => $request->pageTitle(),
            'hero_title' => $request->heroTitle(),
            'hero_badge' => $request->heroBadge(),
            'hero_subtitle' => $request->heroSubtitle(),
            'hero_images' => $this->cmsDanceMapper->normalizeHeroImages($request->heroImages()),
            'highlights_title' => $request->highlightsTitle(),
            'highlights' => $this->cmsDanceMapper->normalizeHighlights($request->highlights()),
            'tracks_title' => $request->tracksTitle(),
            'tracks_note' => $request->tracksNote(),
            'tracks' => $this->cmsDanceMapper->normalizeTracks($request->tracks(), $existingTrackAudioUrls),
            'important_information_title' => $request->importantInformationTitle(),
            'important_information_html' => $this->sanitizeWysiwygField($request->importantInformationHtml()),
        ];
    }

    private function sanitizeWysiwygField(string $value): string
    {
        return $this->htmlSanitizer->sanitizeWysiwygHtml($value);
    }

    private function buildDanceHomePage(array $input): Page
    {
        $page = new Page($input['page_title'], 'dance-home');
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

    private function buildDanceDetailPage(string $pageSlug, array $input): Page
    {
        $page = new Page($input['page_title'], $pageSlug);
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
