<?php
namespace App\Service\Interfaces;

use App\Models\UserModel;

interface IAuthService
{
    public function login(string $username, string $password): UserModel;
    public function register(string $username, string $password): UserModel;
}