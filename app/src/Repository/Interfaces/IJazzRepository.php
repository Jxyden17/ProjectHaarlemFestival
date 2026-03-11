<?php
namespace App\Repository\Interfaces;

use App\Models\JazzEvent;

interface IJazzRepository
{
   # public function findJazzEventById(int $id);
    public function getAllJazzEvents(): array;
   # public function addJazzEvent(JazzEvent $jazzEvent);
   # public function deleteJazzEvent(JazzEvent $jazzEvent): void;
   # public function updateJazzEvent(JazzEvent $jazzEvent);

}