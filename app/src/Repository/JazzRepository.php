<?php

namespace App\Repository;

use App\Models\Database;
use App\Models\EventModel;
use App\Models\VenueModel;
use PDO;
use App\Models\JazzEvent;
use App\Repository\Interfaces\IJazzRepository;


class JazzDummyRepository implements IJazzRepository
{
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

}