<?php

namespace App\Service\Cms;

use App\Repository\Interfaces\IUserRepository;
use App\Models\UserModel;
use App\Service\Cms\Interfaces\ICmsService;

class CmsService implements ICmsService
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

    public function addUser(UserModel $user): UserModel
    {
        if($this->userRepo->findByEmail($user->email)) 
        {
            throw new \Exception("Email is already used.");
        }
        return $this->userRepo->create($user);
    }

    public function deleteUser(int $id): void
    {
        $this->userRepo->deleteUser($id);
    }

    public function updateUser( UserModel $user): UserModel
    {
        if(empty($user->email))
            {
                throw new \Exception("Email can not be empty.");
            }
        return $this->userRepo->updateUser($user);
    }

    public function searchUsers(string $query): array
    {
        return $this->userRepo->searchUsers($query);
    }

    public function sortUsers(string $sortBy, string $sortOrder): array
    {
        return $this->userRepo->sortUsers($sortBy, $sortOrder);
    }

}
