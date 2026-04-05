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
                'schedule' => $page->getSection('schedule'),
                'program'   => $program
            ];

            $this->render('personalProgram/index', $viewData);

        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    public function delete(): void
    {
        header('Content-Type: application/json');

        try {

            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false]);
                return;
            }

            $data = json_decode(file_get_contents('php://input'), true);

            $sessionId = $data['session_id'] ?? null;

            if (!$sessionId) {
                echo json_encode(['success' => false]);
                return;
            }

            $this->personalProgramService->deleteTicket(
                (int)$_SESSION['user_id'],
                (int)$sessionId
            );

            echo json_encode(['success' => true]);

        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
