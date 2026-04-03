<?php

namespace App\Controllers;

use App\Service\Interfaces\IFavoritesService;

class FavoritesController extends BaseController
{
    private IFavoritesService $favoritesService;

    public function __construct(IFavoritesService $favoritesService)
    {
        $this->favoritesService = $favoritesService;
    }

    public function index(): void
    {
        $this->requireAuth();

        $favorites = $this->favoritesService->getFavorites();

        $this->render('favorites/index', [
            'title' => 'Favorites',
            'favorites' => $favorites,
        ]);
    }

    public function add(): void
    {
        $this->requireAuth();

        $sessionId = (int) ($_POST['session_id'] ?? 0);

        try {
            $this->favoritesService->addFavorite($sessionId);
        } catch (\Throwable $e) {
            http_response_code(400);
            $this->render('shared/error', [
                'errorTitle' => 'Favorites unavailable',
                'errorMessage' => $e->getMessage(),
            ]);
            return;
        }

        header('Location: ' . $this->resolveRedirectTarget());
        exit;
    }

    public function remove(): void
    {
        $this->requireAuth();

        $sessionId = (int) ($_POST['session_id'] ?? 0);

        try {
            $this->favoritesService->removeFavorite($sessionId);
        } catch (\Throwable $e) {
            http_response_code(400);
            $this->render('shared/error', [
                'errorTitle' => 'Favorites unavailable',
                'errorMessage' => $e->getMessage(),
            ]);
            return;
        }

        header('Location: ' . $this->resolveRedirectTarget());
        exit;
    }

    private function resolveRedirectTarget(): string
    {
        $redirectTo = trim((string) ($_POST['redirect_to'] ?? ''));

        if ($redirectTo === '' || !str_starts_with($redirectTo, '/')) {
            return '/favorites';
        }

        return $redirectTo;
    }
}
