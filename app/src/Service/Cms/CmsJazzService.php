<?php

namespace App\Service\Cms;




use App\Models\Page\Page;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use App\Service\Cms\Interfaces\ICmsJazzService;
use App\Service\Cms\Interfaces\ICmsPageSaveService;
use App\Service\Interfaces\IHtmlSanitizerService;
use App\Service\Interfaces\IPageService;

class CmsJazzService implements ICmsJazzService
{
    private ICmsPageSaveService $pageSaveService;
    private IPageService $pageService;
    private IHtmlSanitizerService $htmlSanitizer;

    public function __construct(ICmsPageSaveService $pageSaveService, IPageService $pageService, IHtmlSanitizerService $htmlSanitizer)
    {
        $this->pageSaveService = $pageSaveService;
        $this->pageService = $pageService;
        $this->htmlSanitizer = $htmlSanitizer;
    }

    public function getJazzHomePage(): Page
    {
        return $this->pageService->buildPage(28);
    }

}