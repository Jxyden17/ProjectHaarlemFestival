<?php

namespace App\Models\Page;


class Section
{
    public int $id;
    public string $type;
    public string $title;
    public int $order;
    public ?string $subTitle;
    public ?string $description;

    public array $items = [];

    public function __construct(int $id, string $type, string $title,int $order, string $subTitle, string $description)
    {
        $this->id = $id;
        $this->type = $type;
        $this->title = $title;
        $this->order = $order;
        $this->subTitle = $subTitle;
        $this->description = $description;
    }

    public function addItem(SectionItem $item) : void
    {
        $this->items[] = $item;
    }

    public function getItemsByCategorie(string $category) : array
    {
        $items = array_filter($this->items, function(SectionItem $item) use ($category) {
            return $item->category === $category;
        });
        return $items;
    }

    public function getFirstItemImage(string $category): ?string 
    {
        $items = $this->getItemsByCategorie($category);

        // Controleer of het item een afbeelding heeft
        foreach ($items as $item) {
            if (!empty($item->image)) { 
                return $item->image;
            }
        }
    return '/img/historyIMG/hero.png';
    }
}