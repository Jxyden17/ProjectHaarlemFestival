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
        $repository = new YummyRepository();
        $this->yummyService = new YummyService($repository);
    }

    public function index(): void
    {
        $yummyIndexViewModel = $this->yummyService->getYummyPage();

        $this->render('yummy/index', [
            'yummyIndexViewModel' => $yummyIndexViewModel
        ]);
    }
}