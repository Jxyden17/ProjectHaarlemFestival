<?php
namespace App\Repository\Interfaces;

use App\Models\UserModel;

interface IUserRepository
{
    public function findById(int $id): ?UserModel;
    public function findByUsername(string $username): ?UserModel;
    public function create(string $username, string $password): UserModel;
    public function updatePassword(int $userId, string $newPassword): void;
}
