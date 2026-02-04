<?php
namespace App\Service\Interfaces;

use App\Models\UserModel;

interface IAuthService
{
    public function login(string $email, string $password): UserModel;
    public function register(string $email, string $password): UserModel;
}
