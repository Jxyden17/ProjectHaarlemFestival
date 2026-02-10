<?php

namespace App\Service;

use App\Repository\Interfaces\IUserRepository;
use App\Models\UserModel;
use App\Service\Interfaces\IAdminService;

class AdminService implements IAdminService
{
    private IUserRepository $userRepo;

    public function __construct(IUserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function getAllUsers(): array
    {
        return $this->userRepo->getAllUsers();
    }

    public function getUserById(int $id): ?UserModel 
    {
        return $this->userRepo->findById($id);
    }

    public function addUser(string $email, string $password, int $roleId): UserModel
    {
        if($this->userRepo->findByEmail($email)) 
        {
            throw new \Exception("Email is already used.");
        }
        return $this->userRepo->addUsers($email, $password, $roleId);
    }

    public function deleteUser(int $id): void
    {
        $this->userRepo->deleteUser($id);
    }

    public function updateUser(int $id, string $email, string $password, int $roleId): UserModel
    {
        if(empty($email))
            {
                throw new \Exception("Email can not be empty.");
            }
        return $this->userRepo->updateUser($id, $email, $password, $roleId);
    }

    public function searchUsers(string $query): array
    {
        return $this->userRepo->searchUsers($query);
    }
}