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

    // Stores CMS dance dependencies so sanitizing, validation, mapping, and persistence stay coordinated in one service.
    public function __construct(IDanceRepository $danceRepository, ICmsPageSaveService $pageSaveService, IPageService $pageService, IHtmlSanitizerService $htmlSanitizer, CmsDanceMapper $cmsDanceMapper, CmsDanceValidator $danceValidator)
    {
        $this->danceRepository = $danceRepository;
        $this->pageSaveService = $pageSaveService;
        $this->pageService = $pageService;
        $this->htmlSanitizer = $htmlSanitizer;
        $this->cmsDanceMapper = $cmsDanceMapper;
        $this->danceValidator = $danceValidator;
    }

    // Returns the CMS dance home page so the home editor can preload the current content. Example: slug 'dance-home' -> Page.
    public function getDanceHomePage(): Page
    {
        return $this->pageService->getPageBySlug('dance-home', 'Dance Home');
    }

    // Saves the dance home CMS form so HTML is sanitized, rows are normalized, and only valid content reaches storage.
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

        $this->pageSaveService->savePageContentBySlug(
            'dance-home',
            $request->pageTitle(),
            $this->cmsDanceMapper->mapHomeRequestSectionsForSave(
                $request,
                $passItems,
                $bannerDescription,
                $importantInformationHtml,
                $capacityHtml,
                $specialHtml
            ),
            'Dance home page not found.'
        );
    }

    // Builds the CMS editor payload for one dance detail slug so the edit screen gets page content and display metadata together. Example: slug 'urban-echo' -> DanceDetailEditorData.
    public function getDanceDetailEditorData(string $pageSlug): DanceDetailEditorData
    {
        $meta = $this->resolveDetailPageMeta($pageSlug);
        $page = $this->pageService->requirePageBySlug($meta->pageSlug, 'Dance detail content page not found.');
        $performerName = $this->resolveDetailPerformerName($meta, $page->getSection('dance_detail_hero'));

        return new DanceDetailEditorData(
            $meta,
            $page,
            $this->buildEditorTitle($meta),
            $performerName
        );
    }

    // Saves one dance detail CMS form so mapped sections update the correct page and existing track audio is preserved.
    public function saveDanceDetailPage(string $pageSlug, UpdateDanceDetailRequest $request): void
    {
        $meta = $this->resolveDetailPageMeta($pageSlug);
        $existingTrackAudioUrls = $this->getExistingTrackAudioUrlsByItemId(
            $this->pageService->requirePageBySlug($meta->pageSlug, 'Dance detail content page not found.')
        );
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

        $this->pageSaveService->savePageContent(
            $meta->pageId,
            $request->pageTitle(),
            $this->cmsDanceMapper->mapDetailRequestSectionsForSave(
                $request,
                $heroImages,
                $highlights,
                $tracks,
                $importantInformationHtml
            )
        );
    }

    // Resolves required detail metadata so CMS flows fail fast when a slug does not match a known dance page.
    private function resolveDetailPageMeta(string $pageSlug): EventDetailPageModel
    {
        $meta = $this->danceRepository->findDetailPageByPageSlug($pageSlug);
        if ($meta === null) {
            throw new \InvalidArgumentException('Unknown dance detail page.');
        }

        return $meta;
    }

    // Builds the CMS editor title so unnamed performers still get a stable fallback heading. Example: performer 'Mina' -> 'Mina Detail Content'.
    private function buildEditorTitle(EventDetailPageModel $meta): string
    {
        $performerName = trim((string)($meta->performerName ?? ''));
        if ($performerName === '') {
            return 'Dance Detail Content';
        }

        return $performerName . ' Detail Content';
    }

    // Resolves the performer label so the editor can fall back to the hero title when metadata is empty.
     private function resolveDetailPerformerName(EventDetailPageModel $meta, ?Section $hero): string
    {
        $performerName = trim((string)($meta->performerName ?? ''));
        if ($performerName !== '') {
            return $performerName;
        }

        return $hero === null ? '' : trim((string)$hero->title);
    }

    // Sanitizes one WYSIWYG value so stored HTML keeps allowed markup and strips unsafe content.
    private function sanitizeWysiwygField(string $value): string
    {
        return $this->htmlSanitizer->sanitizeWysiwygHtml($value);
    }

    // Reads current track audio URLs by item id so detail saves do not blank audio when the form omits unchanged files.
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
}
