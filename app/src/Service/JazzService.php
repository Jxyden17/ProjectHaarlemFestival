<?php

namespace App\Service;

use App\Repository\Interfaces\IJazzRepository;
use App\Repository\scheduleRepository;
use App\Models\JazzEvent;
use App\Service\Interfaces\IJazzService;

class JazzService implements IJazzService
{
    private IJazzRepository $jazzRepo;
    private ScheduleRepository $scheduleRepo;

    public function __construct(IJazzRepository $jazzRepo,ScheduleRepository $scheduleRepo)
    {
        $this->jazzRepo = $jazzRepo;
        $this->scheduleRepo = $scheduleRepo;
    }
    public function getAllJazzEvents():array
    {
        return $this->jazzRepo->getAllJazzEvents();

    }
    public function getAllJazzPerformers()
    {
        return$this->scheduleRepo->getPerformersByEventId(5);

    }
}