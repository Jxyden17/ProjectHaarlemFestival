<?php
namespace App\Service\Interfaces;

use App\Models\JazzEvent;

interface IJazzService
{
   # public function findJazzEventById(int $id);
    public function getAllJazzEvents(): array;
   # public function addJazzEvent(JazzEvent $jazzEvent);
   # public function deleteJazzEvent(JazzEvent $jazzEvent): void;
   # public function updateJazzEvent(JazzEvent $jazzEvent);

}