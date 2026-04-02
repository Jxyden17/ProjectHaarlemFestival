<?php

namespace App\Controllers;

use App\Service\Interfaces\ICartService;

class BookController extends BaseController
{
    private ICartService $cartService;

    public function __construct(ICartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(array $vars): void
    {
        $sessionId = (int) ($vars['sessionId'] ?? 0);

        if ($sessionId <= 0) {
            http_response_code(404);
            $this->render('shared/error', [
                'errorTitle' => 'Session not found',
                'errorMessage' => 'The session you requested does not exist.',
            ]);
            return;
        }

        $session = $this->cartService->getSessionForBooking($sessionId);

        if ($session === null) {
            http_response_code(404);
            $this->render('shared/error', [
                'errorTitle' => 'Session not found',
                'errorMessage' => 'The session you requested does not exist.',
            ]);
            return;
        }

        $this->render('book/index', [
            'title' => 'Book Tickets',
            'session' => $session,
        ]);
    }
}
