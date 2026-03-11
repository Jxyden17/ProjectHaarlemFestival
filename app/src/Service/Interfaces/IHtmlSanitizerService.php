<?php

namespace App\Service\Interfaces;

interface IHtmlSanitizerService
{
    public function sanitizeWysiwygHtml(string $html): string;
}
