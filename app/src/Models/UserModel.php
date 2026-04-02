<?php

namespace App\Models;

use App\Models\Enums\UserRole;

class UserModel
{
    public int $id;
    public string $name;
    public string $email;
    public string $password;
    public string $phoneNumber;
    public string $country;
    public string $city;
    public string $addres;
    public string $postcode;
    public UserRole $userRole;
    public string $createdAt;

    public function __construct(int $id,string $name, string $email, string $password,string $phoneNumber,string $country,string $city,string $addres, string $postcode, int $userRole, string $createdAt)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->phoneNumber = $phoneNumber;
        $this->country = $country;
        $this->city = $city;
        $this->addres = $addres;
        $this->postcode = $postcode;
        $this->userRole = UserRole::from($userRole);
        $this->createdAt = $createdAt;
    }
}
