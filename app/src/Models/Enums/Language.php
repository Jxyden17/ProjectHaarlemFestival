<?php

namespace App\Models\Enums;

enum Language: int
{
    case English = 1;
    case Dutch = 2;
    case Chinese = 3;

    public function label(): string
    {
        return match ($this) {
            self::English => 'English',
            self::Dutch => 'Dutch',
            self::Chinese => 'Chinese',
        };
    }   
}