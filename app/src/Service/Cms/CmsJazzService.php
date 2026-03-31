<?php

namespace App\Service\Cms;




use App\Models\Page\Page;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use App\Service\Cms\Interfaces\ICmsJazzService;
use App\Service\Cms\Interfaces\ICmsPageSaveService;
use App\Service\Interfaces\IHtmlSanitizerService;
use App\Service\Interfaces\IPageService;
<<<<<<< Updated upstream
use App\Mapper\CmsJazzMapper;
use App\Models\Requests\UpdateJazzHomeRequest;
=======
>>>>>>> Stashed changes

class CmsJazzService implements ICmsJazzService
{
    private ICmsPageSaveService $pageSaveService;
    private IPageService $pageService;
    private IHtmlSanitizerService $htmlSanitizer;
<<<<<<< Updated upstream
    private CmsJazzMapper $cmsJazzMapper;

    public function __construct(ICmsPageSaveService $pageSaveService, IPageService $pageService, IHtmlSanitizerService $htmlSanitizer, CmsJazzMapper $cmsJazzMapper)
=======

    public function __construct(ICmsPageSaveService $pageSaveService, IPageService $pageService, IHtmlSanitizerService $htmlSanitizer)
>>>>>>> Stashed changes
    {
        $this->pageSaveService = $pageSaveService;
        $this->pageService = $pageService;
        $this->htmlSanitizer = $htmlSanitizer;
<<<<<<< Updated upstream
        $this->cmsJazzMapper = $cmsJazzMapper;
=======
>>>>>>> Stashed changes
    }

    public function getJazzHomePage(): Page
    {
        return $this->pageService->buildPage(28);
    }
<<<<<<< Updated upstream
    public function saveJazzHomePage($request)
    {
        $bannerDescription = $this->sanitizeWysiwygField($request->bannerDescription());
        $importantInformationHtml = $this->sanitizeWysiwygField($request->importantInformationHtml());
        $passItems = $this->cmsJazzMapper->normalizePasses($request->passes());
        $capacityHtml = $this->sanitizeWysiwygField($request->capacityHtml());
        $specialHtml = $this->sanitizeWysiwygField($request->specialHtml());

        $page = $this->buildJazzHomePage(
            $request,
            $passItems,
            $bannerDescription,
            $importantInformationHtml,
            $capacityHtml,
            $specialHtml
        );
        $this->persistJazzHomePage($page);
    }
    private function sanitizeWysiwygField(string $value): string
    {
        return $this->htmlSanitizer->sanitizeWysiwygHtml($value);
    }
    private function buildJazzHomePage(UpdateJazzHomeRequest $request, array $passItems, string $bannerDescription, string $importantInformationHtml, string $capacityHtml, string $specialHtml): Page
    {
        $page = new Page($request->pageTitle(), 'Jazz-home');
        $page->sections = [
            new Section(0, 'jazz_schedule', $request->scheduleTitle(), '', ''),
            new Section(0, 'jazz_banner', $request->bannerTitle(), $request->bannerBadge(), $bannerDescription),
            new Section(0, 'jazz_artists', $request->featuredArtistsTitle(), '', ''),
            new Section(0, 'jazz_info', $request->importantInformationTitle(), '', $importantInformationHtml),
            new Section(0, 'jazz_passes', $request->passesTitle(), '', ''),
            new Section(0, 'jazz_capacity', $request->capacityTitle(), '', $capacityHtml),
            new Section(0, 'jazz_special_session', $request->specialTitle(), '', $specialHtml),
        ];

        $this->appendSectionItems($page, 'Jazz_passes', $passItems);

        return $page;
    }
     private function persistJazzHomePage(Page $page): void
    {
        $this->pageSaveService->savePageContentBySlug(
            'Jazz-home',
            $page->title,
            $this->cmsJazzMapper->mapHomeSectionsForSave($page),
            'Jazz home page not found.'
        );
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

=======
>>>>>>> Stashed changes

}