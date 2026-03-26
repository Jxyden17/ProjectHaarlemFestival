<?php

namespace App\Repository\Interfaces;


interface IPersonalProgramRepository
{
    public function getUserTicketsWithSessions(int $userId): array;

    public function deleteSession(int $userId, int $sessionId): void;
}