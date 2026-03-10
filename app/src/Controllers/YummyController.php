<?php

namespace App\Controllers;

use App\Service\YummyService;

class YummyController extends BaseController
{
    private YummyService $yummyService;

    public function __construct(YummyService $yummyService)
    {
        $this->yummyService = $yummyService;
    }

    public function index(): void
    {
        $yummyIndexViewModel = $this->yummyService->getYummyPage();

        $this->render('yummy/index', [
            'yummyIndexViewModel' => $yummyIndexViewModel
        ]);
    }

    public function restaurant(array $vars = []): void
    {
        $slug = trim((string)($vars['slug'] ?? ''));
        $viewModel = $this->yummyService->getRestaurantPage($slug);

        if (!$viewModel) {
            http_response_code(404);
            echo "Restaurant not found";
            return;
        }

        $this->render('yummy/restaurant', [
            'viewModel' => $viewModel
        ]);
    }
}
