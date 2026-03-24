<?php

namespace App\Controllers;

use App\Service\Interfaces\IPageService;
use App\Service\PersonalProgramService;

class PersonalProgramController extends BaseController
{
    private IPageService $pageService;
    private PersonalProgramService $personalProgramService;

    public function __construct(
        IPageService $pageService,
        PersonalProgramService $personalProgramService
    ) {
        $this->pageService = $pageService;
        $this->personalProgramService = $personalProgramService;
    }

    public function index(): void
    {
        try {
            if (!isset($_SESSION['user_id'])) {
                header('Location: /login');
                exit;
            }

            $page = $this->pageService->getPageBySlug('personal-program', 'Personal Program');

            $program = $this->personalProgramService->getPersonalProgram($_SESSION['user_id']);


            $viewData = [
                'pageTitle' => $page->title,
                'hero'      => $page->getSection('hero'),
                'program'   => $program
            ];

            $this->render('personalProgram/index', $viewData);

        }
        catch (\Exception $e) {
            var_dump($e->getMessage());
            die();
        }
    }
}