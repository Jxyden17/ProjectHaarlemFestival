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
            'callout'   => $page->getSection('callout'),
            'grid'      => $page->getSection('grid'),
            'venues'    => $page->getSection('venues'),
            'schedule'  => $page->getSection('schedule'),
            'explore'   => $page->getSection('explore'),
            'faq'       => $page->getSection('faq')
        ];
        $this->render('Stories/index', $viewData);
    }

    public function details($slug = null): void
    {
        // If no slug provided, try to get it from GET parameter
        if (!$slug) {
            $slug = isset($_GET['slug']) ? $_GET['slug'] : null;
        }

        if (!$slug) {
            http_response_code(404);
            $this->render('shared/error', [
                'errorTitle' => 'Page not found',
                'errorMessage' => 'The page you requested does not exist.',
            ]);
            return;
        }

        $viewData = [
            'pageTitle' => ucfirst(str_replace('-', ' ', $slug)),
            'slug' => $slug
        ];

        http_response_code(200);
        echo "Profile of: " . htmlspecialchars($slug);
    }
}