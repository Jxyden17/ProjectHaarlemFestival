<?php

namespace App\Service\Cms;

use App\Mapper\CmsDanceViewModelMapper;
use App\Mapper\CmsDanceMapper;
use App\Models\Dance\DanceDetailEditInput;
use App\Models\Dance\DanceHomeEditInput;
use App\Models\Event\EventDetailPageModel;
use App\Models\Page\Page;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use App\Models\Requests\UpdateDanceDetailRequest;
use App\Models\Requests\UpdateDanceHomeRequest;
use App\Models\ViewModels\Cms\Dance\DanceDetailEditViewModel;
use App\Models\ViewModels\Cms\Dance\DanceHomeEditViewModel;
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
    private CmsDanceViewModelMapper $cmsDanceViewModelMapper;
    private CmsDanceValidator $danceValidator;

    public function __construct(IDanceRepository $danceRepository, ICmsPageSaveService $pageSaveService, IPageService $pageService, IHtmlSanitizerService $htmlSanitizer, CmsDanceMapper $cmsDanceMapper, CmsDanceViewModelMapper $cmsDanceViewModelMapper, CmsDanceValidator $danceValidator)
    {
        $this->danceRepository = $danceRepository;
        $this->pageSaveService = $pageSaveService;
        $this->pageService = $pageService;
        $this->htmlSanitizer = $htmlSanitizer;
        $this->cmsDanceMapper = $cmsDanceMapper;
        $this->cmsDanceViewModelMapper = $cmsDanceViewModelMapper;
        $this->danceValidator = $danceValidator;
    }

    public function getDanceHomeFormData(): DanceHomeEditViewModel
    {
        $page = $this->getDanceHomePage();
        return $this->cmsDanceViewModelMapper->mapHomeContentViewModelFromPage($page);
    }

    public function saveDanceHomePage(UpdateDanceHomeRequest $request): void
    {
        $homeEditInput = $this->buildDanceHomeEditInput($request);
        $this->danceValidator->validateHomePageInput($homeEditInput);
        $page = $this->buildDanceHomePage($homeEditInput);
        $this->persistDanceHomePage($page);
    }

    public function getDanceDetailFormData(string $pageSlug): DanceDetailEditViewModel
    {
        $meta = $this->resolveDetailPageMeta($pageSlug);
        $page = $this->pageService->getPageBySlug($meta->pageSlug, $this->buildEditorTitle($meta));
        $performerName = $this->resolveDetailPerformerName($meta, $page->getSection('dance_detail_hero'));

        return $this->cmsDanceViewModelMapper->mapDetailContentViewModel(
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
        $detailEditInput = $this->buildDanceDetailEditInput($request, $existingTrackAudioUrls);
        $this->danceValidator->validateDetailPageInput($detailEditInput);
        $page = $this->buildDanceDetailPage($meta->pageSlug, $detailEditInput);
        $page->id = $existingPage->id;
        $this->persistDanceDetailPage($page);
    }

    private function getDanceHomePage(): Page
    {
        return $this->pageService->getPageBySlug('dance-home', 'Dance Home');
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

    private function buildDanceHomeEditInput(UpdateDanceHomeRequest $request): DanceHomeEditInput
    {
        return new DanceHomeEditInput(
            $request->pageTitle(),
            $request->scheduleTitle(),
            $request->featuredArtistsTitle(),
            $request->bannerBadge(),
            $request->bannerTitle(),
            $this->sanitizeWysiwygField($request->bannerDescription()),
            $request->importantInformationTitle(),
            $this->sanitizeWysiwygField($request->importantInformationHtml()),
            $request->passesTitle(),
            $this->cmsDanceMapper->normalizePasses($request->passes()),
            $request->capacityTitle(),
            $this->sanitizeWysiwygField($request->capacityHtml()),
            $request->specialTitle(),
            $this->sanitizeWysiwygField($request->specialHtml())
        );
    }

    private function buildDanceDetailEditInput(UpdateDanceDetailRequest $request, array $existingTrackAudioUrls = []): DanceDetailEditInput
    {
        return new DanceDetailEditInput(
            $request->pageTitle(),
            $request->heroTitle(),
            $request->heroBadge(),
            $request->heroSubtitle(),
            $this->cmsDanceMapper->normalizeHeroImages($request->heroImages()),
            $request->highlightsTitle(),
            $this->cmsDanceMapper->normalizeHighlights($request->highlights()),
            $request->tracksTitle(),
            $request->tracksNote(),
            $this->cmsDanceMapper->normalizeTracks($request->tracks(), $existingTrackAudioUrls),
            $request->importantInformationTitle(),
            $this->sanitizeWysiwygField($request->importantInformationHtml())
        );
    }

    private function sanitizeWysiwygField(string $value): string
    {
        return $this->htmlSanitizer->sanitizeWysiwygHtml($value);
    }

    private function buildDanceHomePage(DanceHomeEditInput $input): Page
    {
        $page = new Page($input->pageTitle, 'dance-home');
        $page->sections = [
            new Section(0, 'dance_schedule', $input->scheduleTitle, '', ''),
            new Section(0, 'dance_banner', $input->bannerTitle, $input->bannerBadge, $input->bannerDescription),
            new Section(0, 'dance_artists', $input->featuredArtistsTitle, '', ''),
            new Section(0, 'dance_info', $input->importantInformationTitle, '', $input->importantInformationHtml),
            new Section(0, 'dance_passes', $input->passesTitle, '', ''),
            new Section(0, 'dance_capacity', $input->capacityTitle, '', $input->capacityHtml),
            new Section(0, 'dance_special_session', $input->specialTitle, '', $input->specialHtml),
        ];

        $this->appendSectionItems($page, 'dance_passes', $input->passItems);

        return $page;
    }

    private function buildDanceDetailPage(string $pageSlug, DanceDetailEditInput $input): Page
    {
        $page = new Page($input->pageTitle, $pageSlug);
        $page->sections = [
            new Section(0, 'dance_detail_hero', $input->heroTitle, $input->heroBadge, $input->heroSubtitle),
            new Section(0, 'dance_detail_highlights', $input->highlightsTitle, '', ''),
            new Section(0, 'dance_detail_tracks', $input->tracksTitle, '', $input->tracksNote),
            new Section(0, 'dance_detail_info', $input->importantInformationTitle, '', $input->importantInformationHtml),
        ];

        $this->appendSectionItems($page, 'dance_detail_hero', $input->heroImages);
        $this->appendSectionItems($page, 'dance_detail_highlights', $input->highlights);
        $this->appendSectionItems($page, 'dance_detail_tracks', $input->tracks);

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
