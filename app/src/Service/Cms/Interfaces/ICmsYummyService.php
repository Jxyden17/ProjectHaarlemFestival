<?php

namespace App\Service\Cms\Interfaces;

interface ICmsYummyService
{
    public function saveYummyContent(array $sections, array $items): void;

    public function savePageContentBySlug(string $slug, array $sections, array $items): void;
}