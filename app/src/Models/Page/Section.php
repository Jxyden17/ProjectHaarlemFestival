<?php

namespace App\Models\Page;


class Section
{
    public int $id;
    public string $type;
    public string $title;
    public ?string $subTitle;
    public ?string $description;

    public array $items = [];

    public function __construct(int $id, string $type, string $title, string $subTitle, string $description)
    {
        $this->id = $id;
        $this->type = $type;
        $this->title = $title;
        $this->subTitle = $subTitle;
        $this->description = $description;
    }

    public function addItem(SectionItem $item) : void
    {
        $this->items[] = $item;
    }

    public function getItemsByCategorie(string $category) : array
    {
        return array_filter($this->items, function(SectionItem $item) use ($category)
        {
            return $item->category === $category;
        });
    }
}