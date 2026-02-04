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

    public function login(string $email, string $password): UserModel
    {
        $user = $this->userRepo->findByEmail($email);

        if (!$user) {
            throw new \Exception("User not found");
        }

        if (!password_verify($password, $user->password)) {
            throw new \Exception("Invalid password");
        }

        return $user;
    }

    public function register(string $email, string $password): UserModel
    {
        // Check if email already exists
        $existing = $this->userRepo->findByEmail($email);
        if ($existing) {
            throw new \Exception("Email already taken");
        }

        // Create new user
        return $this->userRepo->create($email, $password);
    }
}
