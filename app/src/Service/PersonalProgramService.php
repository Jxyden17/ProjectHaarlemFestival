<?php

namespace App\Service;

use App\Service\Interfaces\IPersonalProgramService;
use App\Repository\PersonalProgramRepository;
use App\Mapper\PersonalProgramMapper;

class PersonalProgramService
{
    private PersonalProgramRepository $personalProgramRepo;
    private PersonalProgramMapper $personalProgramMapper;

    public function __construct(
        PersonalProgramRepository $personalProgramRepo,
        PersonalProgramMapper $personalProgramMapper
    ) {
        $this->personalProgramRepo = $personalProgramRepo;
        $this->personalProgramMapper = $personalProgramMapper;
    }

    public function getPersonalProgram(int $userId): array
    {
        $rows = $this->personalProgramRepo->getUserTicketsWithSessions($userId);

        return $this->personalProgramMapper->map($rows);
    }

    public function deleteTicket(int $userId, int $sessionId): void
    {
        $this->personalProgramRepo->deleteSession($userId, $sessionId);
    }
}