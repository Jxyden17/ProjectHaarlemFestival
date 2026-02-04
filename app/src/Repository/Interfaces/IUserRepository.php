<?php
namespace App\Repository\Interfaces;

use App\Models\UserModel;

interface IUserRepository
{
    public function findById(int $id): ?UserModel;
    public function findByEmail(string $email): ?UserModel;
    public function create(string $email, string $password): UserModel;
    public function updatePassword(int $userId, string $newPassword): void;
}
