<?php

namespace App\Controllers;

use App\Models\ViewModels\Yummy\YummyIndexViewModel;
use App\Repository\YummyRepository;
use App\Service\YummyService;

class YummyController extends BaseController
{
    private YummyService $yummyService;

    public function __construct()
    {
        // Manually create dependencies
        $repository = new YummyRepository();
        $this->yummyService = new YummyService($repository);
    }

    public function index(): void
    {
        $venues = $this->yummyService->getYummyVenues();

        $this->render('yummy/index', [
            'title' => 'Yummy',
            'yummyIndexViewModel' => new YummyIndexViewModel($venues),
        ]);
    }
}