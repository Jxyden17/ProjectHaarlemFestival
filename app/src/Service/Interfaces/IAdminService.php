<?php
namespace App\Service\Interfaces;

use App\Models\UserModel;

interface IAdminService
{
    public function getAllUsers(): array;
    public function getUserById(int $id): ?UserModel;
    public function addUser(string $email, string $password, int $roleId): UserModel;
    public function updateUser(int $id, string $email, string $password, int $roleId): UserModel;
    public function deleteUser(int $id): void;
    public function searchUsers(string $query): array;
}
