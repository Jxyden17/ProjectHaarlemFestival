<?php

namespace App\Service\Cms\Interfaces;

interface ICmsYummyEditorService
{
    public function saveYummyContent(array $sections, array $items): void;
}