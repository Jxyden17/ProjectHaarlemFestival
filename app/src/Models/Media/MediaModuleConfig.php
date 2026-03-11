<?php

namespace App\Models\Media;

class MediaModuleConfig
{
    public array $allowedPrefixes;
    public ?string $pageSlug;
    public ?string $sectionType;
    public ?string $itemCategory;

    public function __construct(
        array $allowedPrefixes,
        ?string $pageSlug = null,
        ?string $sectionType = null,
        ?string $itemCategory = null
    ) {
        $this->allowedPrefixes = $allowedPrefixes;
        $this->pageSlug = $pageSlug;
        $this->sectionType = $sectionType;
        $this->itemCategory = $itemCategory;
    }

    public function supportsDatabaseSync(): bool
    {
        return $this->pageSlug !== null
            && $this->sectionType !== null
            && $this->itemCategory !== null;
    }
}
