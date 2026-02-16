<?php

namespace App\Controllers;

use App\Service\Interfaces\IJazzService;

class JazzController extends BaseController
{
    private IJazzService $jazzService;

    public function __construct( IJazzService $jazzService)
    {
        $this->jazzService = $jazzService;
    }
   public function index()
    {
        $jazzEvents=$this->jazzService->getAllJazzEvents();
        $this->render("/Jazz/index",['jazzEvents' => $jazzEvents]);

    }


}
?>