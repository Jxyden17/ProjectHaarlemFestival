<?php

namespace App\Repository\Interfaces;

interface IPasswordResetRepository
{
    public function deleteByUserId(int $userId): void;

    public function createPasswordReset(int $userId, string $tokenHash, string $expiresAt): void;

    public function findValidByTokenHash(string $tokenHash): ?array;

    public function deleteById(int $id): void;
}
