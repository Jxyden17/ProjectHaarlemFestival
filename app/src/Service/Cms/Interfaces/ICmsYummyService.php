<?php

namespace App\Service\Cms\Interfaces;

interface ICmsYummyService
{
    public function saveYummyContent(string $slug, array $sections, array $items): void;
}