<?php

namespace App\Repository;

use App\Models\JazzEvent;
use App\Models\Database;
use App\Repository\Interfaces\IJazzRepository;


class JazzDummyRepository implements IJazzRepository
{
    private array $dDb;

    public function __construct()
    {
        $this->dDb =
        [
            new JazzEvent(1, 1, "gumbo kings", "18:00-19:00", "patronaat, main hall",300,300,15.00,"abc","thursday"),
            new JazzEvent(2, 2, "evolve", "19:30-20:30", "patronaat, main hall",300,300,15.00,"def","thursday"),
            new JazzEvent(3, 1, "ntjam rosie", "21:00-22:00", "patronaat, main hall",300,300,15.00,"ghi","thursday"),
        ];
    }

    public function getAllJazzEvents():array
    {
        return $this->dDb;
    }
}