<?php

namespace App\Models\Enums;

enum Event: int
{
    case Tour = 1;
    case Dance = 2;
    case Stories = 3;
    case Yummy = 4;
    case Jazz = 5;

    public function dbName(): string
    {
        return match ($this) {
            self::Tour => 'A Stroll Through History',
            self::Dance => 'Dance',
            self::Stories => 'TellingStory',
            self::Yummy => 'Yummy',
            self::Jazz => 'Jazz',
            default => 'Unknown',
        };
    }   

     public function label(): string
    {
        return match ($this) {
            self::Tour => 'Tour',
            self::Dance => 'Dance',
            self::Stories => 'Stories',
            self::Yummy => 'Yummy',
            self::Jazz => 'Jazz',
            default => 'Unknown',
        };
    }
}