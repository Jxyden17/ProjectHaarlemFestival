<?php

namespace App\Models\Enums;

enum UserRole: int
{
    case Administrator = 1;
    case Employee = 2;
    case Customer = 3;

    public function label(): string
    {
        return match ($this) {
            self::Administrator => 'Administrator',
            self::Customer => 'Employee',
            self::Employee => 'Customer',
        };
    }   
}