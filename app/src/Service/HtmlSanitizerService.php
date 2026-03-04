<?php

namespace App\Service;

use App\Service\Interfaces\IHtmlSanitizerService;

class HtmlSanitizerService implements IHtmlSanitizerService
{
    public function sanitizeWysiwygHtml(string $html): string
    {
        $trimmed = trim($html);
        if ($trimmed === '') {
            return '';
        }

        $allowedTags = '<p><br><strong><em><u><ul><ol><li><a><h2><h3><h4><blockquote>';
        $sanitized = strip_tags($trimmed, $allowedTags);

        // Remove inline event handlers and style attributes.
        $sanitized = preg_replace('/\s+on[a-z]+\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/i', '', $sanitized) ?? '';
        $sanitized = preg_replace('/\s+style\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/i', '', $sanitized) ?? '';

        // Neutralize javascript: urls in anchors.
        $sanitized = preg_replace('/(<a[^>]*\shref\s*=\s*[\'"])\s*javascript:[^\'"]*([\'"][^>]*>)/i', '$1#$2', $sanitized) ?? '';

        return trim($sanitized);
    }
}
