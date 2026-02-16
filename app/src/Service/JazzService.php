<?php

namespace App\Service;

use App\Repository\Interfaces\IJazzRepository;
use App\Models\JazzEvent;
use App\Service\Interfaces\IJazzService;

class JazzService implements IJazzService
{
    private IJazzRepository $jazzRepo;

    public function __construct(IJazzRepository $jazzRepo)
    {
        $this->jazzRepo = $jazzRepo;
    }
    public function getAllJazzEvents():array
    {
        return $this->jazzRepo->getAllJazzEvents();

    }
}