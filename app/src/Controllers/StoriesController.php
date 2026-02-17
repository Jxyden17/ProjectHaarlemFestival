<?php

namespace App\Controllers;

use App\Repository\PageStorieRepository;

class StoriesController extends BaseController
{
    public function index(): void
    {
        $email = $_SESSION['email'] ?? null;

        $repo = new PageStorieRepository();

        // Get the page by slug
        $page = $repo->findBySlug('stories');

        if (!$page) {
            http_response_code(404);
            $this->render('partialsViews/error', [
                'title' => 'Not found',
                'message' => 'Page "stories" not found',
                'email' => $email
            ]);
            return;
        }

        // Get sections for the page
        $sections = $repo->getSections((int)$page['id']);

        // Get items for each section
        foreach ($sections as &$section) {
            $section['items'] = $repo->getSectionItems((int)$section['id']);
        }
        unset($section);
        
        // Render the view with page and sections data
        $this->render('stories/index', [
            'title' => $page['page_name'] ?? 'Home Stories',
            'email' => $email,
            'page' => $page,
            'sections' => $sections
        ]);
    }
}
