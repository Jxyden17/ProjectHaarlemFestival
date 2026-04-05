<?php
namespace App\Service\Cms\Interfaces;

use App\Models\UserModel;

interface ICmsService
{
    public function getAllUsers(): array;
    public function getUserById(int $id): ?UserModel;
    public function addUser( UserModel $user): UserModel;
    public function updateUser( UserModel $user): UserModel;
    public function deleteUser(int $id): void;
    public function searchUsers(string $query): array;
    public function sortUsers(string $sortBy, string $sortOrder): array;
}
