<?php

namespace App\Repository;

use App\Models\Database;
use App\Repository\Interfaces\IJazzRepository;
use PDO;


class JazzRepository implements IJazzRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllJazzEvents(): array
    {
        return [];
    }
}
