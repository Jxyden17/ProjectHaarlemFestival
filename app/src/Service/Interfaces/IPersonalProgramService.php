<?php

namespace App\Service\Interfaces

public interface IPersonalProgramService
{
    public function getPersonalProgram(int $userId): array;
}