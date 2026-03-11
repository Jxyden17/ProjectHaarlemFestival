<?php

namespace App\Mapper;

use App\Models\Event\EventDetailPageModel;
use App\Models\Page\Page;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use App\Models\Requests\Cms\Dance\DanceDetailHeroImageRowRequest;
use App\Models\Requests\Cms\Dance\DanceDetailHighlightRowRequest;
use App\Models\Requests\Cms\Dance\DanceDetailTrackRowRequest;
use App\Models\Requests\Cms\Dance\DanceHomePassRowRequest;
use App\Models\Requests\Cms\DanceDetailContentRequest;
use App\Models\Requests\Cms\DanceHomeContentRequest;
use App\Models\ViewModels\Cms\Dance\DanceDetailContentViewModel;
use App\Models\ViewModels\Cms\Dance\DanceDetailHeroImageRowViewModel;
use App\Models\ViewModels\Cms\Dance\DanceDetailHighlightRowViewModel;
use App\Models\ViewModels\Cms\Dance\DanceDetailTrackRowViewModel;
use App\Models\ViewModels\Cms\Dance\DanceHomeContentViewModel;
use App\Models\ViewModels\Cms\Dance\DanceHomePassRowViewModel;

class CmsDanceMapper
{
    public function mapHomeContentViewModelFromPage(Page $page): DanceHomeContentViewModel
    {
        $schedule = $page->getSection('dance_schedule');
        $banner = $page->getSection('dance_banner');
        $info = $page->getSection('dance_info');
        $passes = $page->getSection('dance_passes');
        $capacity = $page->getSection('dance_capacity');
        $special = $page->getSection('dance_special_session');

        return new DanceHomeContentViewModel(
            $page->title,
            $schedule !== null ? $schedule->title : '',
            $banner !== null ? (string)$banner->subTitle : '',
            $banner !== null ? $banner->title : '',
            $banner !== null ? (string)$banner->description : '',
            '',
            [],
            $info !== null ? $info->title : '',
            $info !== null ? (string)$info->description : '',
            $passes !== null ? $passes->title : '',
            $this->mapPassViewModels($passes),
            $capacity !== null ? $capacity->title : '',
            $capacity !== null ? (string)$capacity->description : '',
            $special !== null ? $special->title : '',
            $special !== null ? (string)$special->description : ''
        );
    }

    public function mapHomeRequestToContentViewModel(DanceHomeContentRequest $request): DanceHomeContentViewModel
    {
        return new DanceHomeContentViewModel(
            $request->pageTitle(),
            $request->scheduleTitle(),
            $request->bannerBadge(),
            $request->bannerTitle(),
            $request->bannerDescription(),
            '',
            [],
            $request->importantInformationTitle(),
            $request->importantInformationHtml(),
            $request->passesTitle(),
            $this->mapPassRequestViewModels($request->passes()),
            $request->capacityTitle(),
            $request->capacityHtml(),
            $request->specialTitle(),
            $request->specialHtml()
        );
    }

    public function mapDetailContentViewModel(
        EventDetailPageModel $meta,
        Page $page,
        string $editorTitle,
        string $performerName
    ): DanceDetailContentViewModel {
        $hero = $page->getSection('dance_detail_hero');
        $highlights = $page->getSection('dance_detail_highlights');
        $tracks = $page->getSection('dance_detail_tracks');
        $info = $page->getSection('dance_detail_info');

        return new DanceDetailContentViewModel(
            $meta->detailSlug,
            $editorTitle,
            $meta->getPublicPath(),
            $page->title,
            $performerName,
            $performerName,
            $hero !== null ? (string)$hero->subTitle : '',
            $hero !== null ? (string)$hero->description : '',
            $this->mapHeroImageViewModels($hero),
            $highlights !== null ? $highlights->title : '',
            $this->mapHighlightViewModels($highlights),
            $tracks !== null ? $tracks->title : '',
            $tracks !== null ? (string)$tracks->description : '',
            $this->mapTrackViewModels($tracks),
            $info !== null ? $info->title : '',
            $info !== null ? (string)$info->description : ''
        );
    }

    public function mapDetailRequestToContentViewModel(
        DanceDetailContentRequest $request,
        DanceDetailContentViewModel $baseViewModel
    ): DanceDetailContentViewModel {
        return new DanceDetailContentViewModel(
            $baseViewModel->detailSlug,
            $baseViewModel->editorTitle,
            $baseViewModel->publicPath,
            $request->pageTitle(),
            $baseViewModel->performerName,
            $baseViewModel->performerName,
            $request->heroBadge(),
            $request->heroSubtitle(),
            $this->mapHeroImageRequestViewModels($request->heroImages()),
            $request->highlightsTitle(),
            $this->mapHighlightRequestViewModels($request->highlights()),
            $request->tracksTitle(),
            $request->tracksNote(),
            $this->mapTrackRequestViewModels($request->tracks()),
            $request->importantInformationTitle(),
            $request->importantInformationHtml()
        );
    }

    public function mapPassViewModels(?Section $passes): array
    {
        if ($passes === null) {
            return [];
        }

        $rows = [];
        foreach ($passes->getItemsByCategorie('pass') as $item) {
            if (!$item instanceof SectionItem) {
                continue;
            }

            $rows[] = new DanceHomePassRowViewModel(
                $item->id,
                $item->title,
                (string)($item->content ?? ''),
                (string)($item->url ?? '') === 'highlight'
            );
        }

        return $rows;
    }

    public function mapPassRequestViewModels(array $passes): array
    {
        $rows = [];
        foreach ($passes as $pass) {
            if (!$pass instanceof DanceHomePassRowRequest) {
                continue;
            }

            $rows[] = new DanceHomePassRowViewModel(
                $pass->id(),
                $pass->label(),
                $pass->price(),
                $pass->highlight()
            );
        }

        return $rows;
    }

    public function normalizePasses(array $passes): array
    {
        $result = [];
        foreach ($passes as $pass) {
            if (!$pass instanceof DanceHomePassRowRequest) {
                continue;
            }

            $label = $pass->label();
            $price = $pass->price();
            if ($label === '' || $price === '') {
                continue;
            }

            $result[] = new SectionItem(
                $pass->id(),
                $label,
                $price,
                null,
                $pass->highlight() ? 'highlight' : null,
                'pass',
                null,
                null,
                null,
                count($result) + 1
            );
        }

        return $result;
    }

    public function normalizeHeroImages(array $heroImages): array
    {
        $result = [];
        foreach ($heroImages as $image) {
            if (!$image instanceof DanceDetailHeroImageRowRequest) {
                continue;
            }

            $result[] = new SectionItem(
                $image->id(),
                '',
                null,
                $image->image(),
                null,
                'hero_image',
                null,
                null,
                $image->alt(),
                count($result) + 1
            );
        }

        return $result;
    }

    public function normalizeHighlights(array $highlights): array
    {
        $result = [];
        foreach ($highlights as $highlight) {
            if (!$highlight instanceof DanceDetailHighlightRowRequest) {
                continue;
            }

            if ($highlight->title() === '' && $highlight->content() === '') {
                continue;
            }

            $result[] = new SectionItem(
                $highlight->id(),
                $highlight->title(),
                $highlight->content(),
                null,
                null,
                'highlight',
                null,
                $highlight->icon() !== '' ? $highlight->icon() : 'star',
                null,
                count($result) + 1
            );
        }

        return $result;
    }

    public function normalizeTracks(array $tracks, array $existingTrackAudioUrls = []): array
    {
        $result = [];
        foreach ($tracks as $track) {
            if (!$track instanceof DanceDetailTrackRowRequest) {
                continue;
            }

            $audioUrl = trim($track->audioUrl());
            $trackId = $track->id();
            if ($audioUrl === '' && $trackId > 0 && isset($existingTrackAudioUrls[$trackId])) {
                $audioUrl = trim((string)$existingTrackAudioUrls[$trackId]);
            }

            if ($track->title() === '' && $track->subtitle() === '' && $track->year() === '' && $track->image() === '' && $audioUrl === '') {
                continue;
            }

            $result[] = new SectionItem(
                $trackId,
                $track->title(),
                $track->year(),
                $track->image(),
                $audioUrl !== '' ? $audioUrl : null,
                'track',
                null,
                null,
                $track->subtitle(),
                count($result) + 1
            );
        }

        return $result;
    }

    public function mapPassRows(array $passes): array
    {
        $rows = [];
        $index = 1;

        foreach ($passes as $pass) {
            if (!$pass instanceof SectionItem) {
                continue;
            }

            $label = trim($pass->title);
            $price = trim((string)($pass->content ?? ''));
            $highlight = ($pass->url ?? '') === 'highlight';
            if ($label === '' || $price === '') {
                continue;
            }

            $rows[] = [
                'id' => $pass->id,
                'title' => $label,
                'item_subtitle' => null,
                'content' => $price,
                'image_path' => null,
                'link_url' => $highlight ? 'highlight' : null,
                'duration' => null,
                'icon_class' => null,
                'order_index' => $index++,
                'item_category' => 'pass',
            ];
        }

        return $rows;
    }

    public function mapHeroImageRows(array $heroImages): array
    {
        $rows = [];
        $index = 1;

        foreach ($heroImages as $image) {
            if (!$image instanceof SectionItem) {
                continue;
            }

            $rows[] = [
                'id' => $image->id,
                'title' => '',
                'item_subtitle' => trim((string)($image->subTitle ?? '')),
                'content' => null,
                'image_path' => trim((string)($image->image ?? '')),
                'link_url' => null,
                'duration' => null,
                'icon_class' => null,
                'order_index' => $index++,
                'item_category' => 'hero_image',
            ];
        }

        return $rows;
    }

    public function mapHighlightRows(array $highlights): array
    {
        $rows = [];
        $index = 1;

        foreach ($highlights as $highlight) {
            if (!$highlight instanceof SectionItem) {
                continue;
            }

            $rows[] = [
                'id' => $highlight->id,
                'title' => trim($highlight->title),
                'item_subtitle' => null,
                'content' => trim((string)($highlight->content ?? '')),
                'image_path' => null,
                'link_url' => null,
                'duration' => null,
                'icon_class' => trim((string)($highlight->icon ?? '')) ?: 'star',
                'order_index' => $index++,
                'item_category' => 'highlight',
            ];
        }

        return $rows;
    }

    public function mapTrackRows(array $tracks): array
    {
        $rows = [];
        $index = 1;

        foreach ($tracks as $track) {
            if (!$track instanceof SectionItem) {
                continue;
            }

            $rows[] = [
                'id' => $track->id,
                'title' => trim($track->title),
                'item_subtitle' => trim((string)($track->subTitle ?? '')),
                'content' => trim((string)($track->content ?? '')),
                'image_path' => trim((string)($track->image ?? '')),
                'link_url' => trim((string)($track->url ?? '')) !== '' ? trim((string)($track->url ?? '')) : null,
                'duration' => null,
                'icon_class' => null,
                'order_index' => $index++,
                'item_category' => 'track',
            ];
        }

        return $rows;
    }

    public function mapHeroImageViewModels(?Section $heroSection): array
    {
        if ($heroSection === null) {
            return [];
        }

        $rows = [];
        foreach ($heroSection->getItemsByCategorie('hero_image') as $item) {
            if (!$item instanceof SectionItem) {
                continue;
            }

            $rows[] = new DanceDetailHeroImageRowViewModel(
                $item->id,
                trim((string)($item->image ?? '')),
                trim((string)($item->subTitle ?? ''))
            );
        }

        return $rows;
    }

    public function mapHeroImageRequestViewModels(array $heroImages): array
    {
        $rows = [];
        foreach ($heroImages as $image) {
            if (!$image instanceof DanceDetailHeroImageRowRequest) {
                continue;
            }

            $rows[] = new DanceDetailHeroImageRowViewModel($image->id(), $image->image(), $image->alt());
        }

        return $rows;
    }

    public function mapHighlightViewModels(?Section $section): array
    {
        if ($section === null) {
            return [];
        }

        $rows = [];
        foreach ($section->getItemsByCategorie('highlight') as $item) {
            if (!$item instanceof SectionItem) {
                continue;
            }

            $rows[] = new DanceDetailHighlightRowViewModel(
                $item->id,
                trim((string)($item->icon ?? '')),
                trim($item->title),
                trim((string)($item->content ?? ''))
            );
        }

        return $rows;
    }

    public function mapHighlightRequestViewModels(array $highlights): array
    {
        $rows = [];
        foreach ($highlights as $highlight) {
            if (!$highlight instanceof DanceDetailHighlightRowRequest) {
                continue;
            }

            $rows[] = new DanceDetailHighlightRowViewModel(
                $highlight->id(),
                $highlight->icon(),
                $highlight->title(),
                $highlight->content()
            );
        }

        return $rows;
    }

    public function mapTrackViewModels(?Section $section): array
    {
        if ($section === null) {
            return [];
        }

        $rows = [];
        foreach ($section->getItemsByCategorie('track') as $item) {
            if (!$item instanceof SectionItem) {
                continue;
            }

            $rows[] = new DanceDetailTrackRowViewModel(
                $item->id,
                trim($item->title),
                trim((string)($item->subTitle ?? '')),
                trim((string)($item->content ?? '')),
                trim((string)($item->image ?? '')),
                trim((string)($item->url ?? ''))
            );
        }

        return $rows;
    }

    public function mapTrackRequestViewModels(array $tracks): array
    {
        $rows = [];
        foreach ($tracks as $track) {
            if (!$track instanceof DanceDetailTrackRowRequest) {
                continue;
            }

            $rows[] = new DanceDetailTrackRowViewModel(
                $track->id(),
                $track->title(),
                $track->subtitle(),
                $track->year(),
                $track->image(),
                $track->audioUrl()
            );
        }

        return $rows;
    }
}
