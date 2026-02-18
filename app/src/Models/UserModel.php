<?php

namespace App\Models;

use App\Models\Enums\UserRole;

class UserModel
{
    public int $id;
    public string $email;
    public string $password;
    public UserRole $userRole;
    public string $createdAt;

    public function __construct(int $id, string $email, string $password, int $userRole, string $createdAt)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->userRole = UserRole::from($userRole);
        $this->createdAt = $createdAt;
    }
}
