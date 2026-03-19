<?php

namespace App\Models\Media;

class MediaModuleConfig
{
    public const MATCH_BY_SECTION = 'match_by_section';
    public const MATCH_BY_SECTION_AND_CATEGORY = 'match_by_section_and_category';

    public array $allowedPrefixes;
    public string $pageSlug;
    public string $sectionType;
    public ?string $itemCategory;
    public string $matchTarget;

    public function __construct(
        array $allowedPrefixes,
        string $pageSlug,
        string $sectionType,
        ?string $itemCategory,
        string $matchTarget
    ) {
        $this->allowedPrefixes = $allowedPrefixes;
        $this->pageSlug = $pageSlug;
        $this->sectionType = $sectionType;
        $this->itemCategory = $itemCategory;
        $this->matchTarget = $matchTarget;
    }

    public function matchesBySectionAndCategory(): bool
    {
        return $this->matchTarget === self::MATCH_BY_SECTION_AND_CATEGORY;
    }

    public function matchesBySection(): bool
    {
        return $this->matchTarget === self::MATCH_BY_SECTION;
    }
}
