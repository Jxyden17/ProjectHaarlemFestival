<?php

namespace App\Service\Interfaces;

interface IPersonalProgramService
{
    public function getPersonalProgram(int $userId): array;

    public function deleteTicket(int $userId, int $sessionId): void;
}