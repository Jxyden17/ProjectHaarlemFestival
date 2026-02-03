<?php

namespace App\Service;

use App\Repository\Interfaces\IUserRepository;
use App\Models\UserModel;
use App\Service\Interfaces\IAuthService;

class AuthService implements IAuthService
{
    private IUserRepository $userRepo;

    public function __construct(IUserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function login(string $username, string $password): UserModel
    {
        $user = $this->userRepo->findByUsername($username);

        if (!$user) {
            throw new \Exception("User not found");
        }

        if (!password_verify($password, $user->password)) {
            throw new \Exception("Invalid password");
        }

        return $user;
    }

    public function register(string $username, string $password): UserModel
    {
        // Check if username already exists
        $existing = $this->userRepo->findByUsername($username);
        if ($existing) {
            throw new \Exception("Username already taken");
        }

        // Create new user
        return $this->userRepo->create($username, $password);
    }
}
