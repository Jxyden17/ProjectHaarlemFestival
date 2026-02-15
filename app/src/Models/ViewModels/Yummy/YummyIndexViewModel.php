<?php

namespace App\Models\ViewModels\Yummy;

class YummyIndexViewModel
{
    public function __construct(
        public array $venues
    ) {}
}