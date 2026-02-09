<?php

namespace App\Models\Enums;

enum UserRole: int
{
    case Administrator = 1;
    case Customer = 2;
    case Employee = 3;

    public function label(): string
    {
        return match ($this) {
            self::Administrator => 'Administrator',
            self::Customer => 'Customer',
            self::Employee => 'Employee',
        };
    }   
}