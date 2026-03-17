<?php

namespace App\Models\Media;

class MediaModuleConfig
{
    public const DATABASE_TARGET_SECTION = 'section';
    public const DATABASE_TARGET_SECTION_ITEM = 'section_item';

    public array $allowedPrefixes;
    public string $pageSlug;
    public string $sectionType;
    public ?string $itemCategory;
    public string $databaseTarget;

    public function __construct(
        array $allowedPrefixes,
        string $pageSlug,
        string $sectionType,
        ?string $itemCategory,
        string $databaseTarget
    ) {
        $this->allowedPrefixes = $allowedPrefixes;
        $this->pageSlug = $pageSlug;
        $this->sectionType = $sectionType;
        $this->itemCategory = $itemCategory;
        $this->databaseTarget = $databaseTarget;
    }

    public function targetsSectionItem(): bool
    {
        return $this->databaseTarget === self::DATABASE_TARGET_SECTION_ITEM;
    }

    public function targetsSection(): bool
    {
        return $this->databaseTarget === self::DATABASE_TARGET_SECTION;
    }
}
