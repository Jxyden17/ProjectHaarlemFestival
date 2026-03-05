<?php

namespace App\Controllers;

use App\Service\Interfaces\IPageService;

class StoriesController extends BaseController
{
    private IPageService $pageService;

    public function __construct(IPageService $pageService) 
    {
        $this->pageService = $pageService;
    }

    public function index(): void
    {
        $pageId = 3;
        
        $page = $this->pageService->buildPage($pageId);
        if (!$page) {
            http_response_code(404);
            $this->render('shared/error', [
                'errorTitle' => 'Page not found',
                'errorMessage' => 'The page you requested does not exist.',
            ]);
            return;
        }

        $viewData = [
            'pageTitle' => $page->title,
            'hero'      => $page->getSection('hero'),
            'grid'      => $page->getSection('grid'),
            'venues'    => $page->getSection('venues'),
            'schedule'  => $page->getSection('schedule'),
            'explore'   => $page->getSection('explore'),
            'faq'       => $page->getSection('faq')
        ];
        $this->render('Stories/index', $viewData);
    }

    public function details(): void
    {
        $pageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$pageId) {
            http_response_code(404);
            $this->render('shared/error', [
                'errorTitle' => 'Page not found',
                'errorMessage' => 'The page you requested does not exist.',
            ]);
            return;
        }

        $page = $this->pageService->buildPage($pageId);

        if (!$page) {
            http_response_code(404);
            $this->render('shared/error', [
                'errorTitle' => 'Page not found',
                'errorMessage' => 'The page you requested does not exist.',
            ]);
            return;
        }

        $viewData = [
            'pageTitle' => $page->title,
            'hero'      => $page->getSection('hero'),
            'grid'      => $page->getSection('grid'),
            'venues'    => $page->getSection('venues'),
            'schedule'  => $page->getSection('schedule'),
            'explore'   => $page->getSection('explore'),
            'faq'       => $page->getSection('faq')
        ];

        $this->render('Stories/details', $viewData);
    }
}