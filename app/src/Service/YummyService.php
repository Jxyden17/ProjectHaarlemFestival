<?php

namespace App\Service;

use App\Repository\YummyRepository;
use App\Models\ViewModels\Yummy\YummyIndexViewModel;

class YummyService
{
    private YummyRepository $repository;

    public function __construct(YummyRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getYummyPage(): YummyIndexViewModel
    {
        $page = $this->repository->getPageBySlug('yummy');

        $hero = $page?->getSection('yummy_header') ?? $page?->getSection('hero');
        $map = $page?->getSection('yummy-map') ?? $page?->getSection('map');
        $restaurants = $page?->getSection('yummy-restaurants') ?? $page?->getSection('restaurants');

        $event = $this->repository->findYummyEvent();
        $venues = $event ? $this->repository->getVenuesByEventId($event->id) : [];

        return new YummyIndexViewModel(
            $venues,
            $hero,
            $map,
            $restaurants
        );
    }


    public function getYummyVenues(): array
    {
        $event = $this->repository->findYummyEvent();

        if ($event === null) {
            return [];
        }

        return $this->repository->getVenuesByEventId($event->id);
    }

    public function getRestaurantPage(string $slug)
    {
        $page = $this->repository->getPageBySlug($slug);

        if (!$page) {
            return null;
        }

        return $page;
    }
}