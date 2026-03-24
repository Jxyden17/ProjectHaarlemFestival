<?php

namespace App\Repository\Interfaces;


interface IPersonalProgramRepository
{
    public function getUserTicketsWithSessions(int $userId): array;
}