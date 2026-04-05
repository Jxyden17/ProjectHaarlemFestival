<?php

namespace App\Mapper;

use App\Models\Event\EventDetailPageModel;
use App\Models\Page\Page;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use App\Models\ViewModels\Cms\Jazz\JazzDetailEditViewModel;
use App\Models\ViewModels\Cms\Jazz\JazzDetailHeroImageRowViewModel;
use App\Models\ViewModels\Cms\Jazz\JazzDetailHighlightRowViewModel;
use App\Models\ViewModels\Cms\Jazz\JazzDetailTrackRowViewModel;
use App\Models\ViewModels\Cms\Jazz\JazzHomeEditViewModel;
use App\Models\ViewModels\Cms\Jazz\JazzHomePassRowViewModel;
use App\Models\Requests\UpdateJazzHomeRequest;

class CmsJazzViewModelMapper
{
    private const SECTION_SCHEDULE = 'jazz_schedule';
    private const SECTION_ARTISTS = 'jazz_artists';
    private const SECTION_PASSES = 'jazz_passes';
    private const SECTION_DETAIL_HERO = 'jazz_detail_hero';
    private const SECTION_DETAIL_HIGHLIGHTS = 'jazz_detail_highlights';
    private const SECTION_DETAIL_TRACKS = 'jazz_detail_tracks';
    private const SECTION_DETAIL_INFO = 'jazz_detail_info';
    private const ITEM_CATEGORY_PASS = 'pass';
    private const ITEM_CATEGORY_HERO_IMAGE = 'hero_image';
    private const ITEM_CATEGORY_HIGHLIGHT = 'highlight';
    private const ITEM_CATEGORY_TRACK = 'track';

    public function mapHomePageToEditViewModel(Page $page): JazzHomeEditViewModel
    {
        $schedule = $page->getSection(self::SECTION_SCHEDULE);
        $artists = $page->getSection(self::SECTION_ARTISTS);
        $passes = $page->getSection(self::SECTION_PASSES);

        return new JazzHomeEditViewModel(
            $page->title,
            $schedule !== null ? $schedule->title : '',
            $artists !== null ? $artists->title : '',
            $passes !== null ? $passes->title : '',
            $this->mapPassViewModels($passes)
        );
    }

    public function mapHomeRequestToEditViewModel(UpdateJazzHomeRequest $request): JazzHomeEditViewModel
    {
        return new JazzHomeEditViewModel(
            $request->pageTitle(),
            $request->scheduleTitle(),
            $request->featuredArtistsTitle(),
            $request->passesTitle(),
            $this->mapPassRequestViewModels($request->passes()),
        );
    }

    public function mapDetailDataToEditViewModel(JazzDetailEditorData $detailData): DanceDetailEditViewModel
    {
        $meta = $detailData->detailMeta;
        $page = $detailData->contentPage;
        $editorTitle = $detailData->editorTitle;
        $performerName = $detailData->performerName;
        $hero = $page->getSection(self::SECTION_DETAIL_HERO);
        $highlights = $page->getSection(self::SECTION_DETAIL_HIGHLIGHTS);
        $tracks = $page->getSection(self::SECTION_DETAIL_TRACKS);
        $info = $page->getSection(self::SECTION_DETAIL_INFO);

        return new JazzDetailEditViewModel(
            $meta->pageSlug,
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

    public function mapDetailRequestToEditViewModel(UpdateJazzDetailRequest $request, JazzDetailEditViewModel $baseViewModel):JazzDetailEditViewModel
    {
        return new JazzDetailEditViewModel(
            $baseViewModel->pageSlug,
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

    private function mapPassViewModels(?Section $passes): array
    {
        if ($passes === null) {
            return [];
        }

        $rows = [];
        foreach ($passes->getItemsByCategorie(self::ITEM_CATEGORY_PASS) as $item) {
            if (!$item instanceof SectionItem) {
                continue;
            }

            $rows[] = new JazzHomePassRowViewModel(
                $item->id,
                $item->title,
                (string)($item->content ?? ''),
                (string)($item->url ?? '') === 'highlight'
            );
        }

        return $rows;
    }

    private function mapPassRequestViewModels(array $passes): array
    {
        $rows = [];
        foreach ($passes as $pass) {
            if (!$pass instanceof JazzHomePassEditRow) {
                continue;
            }

            $rows[] = new JazzHomePassRowViewModel(
                $pass->id(),
                $pass->label(),
                $pass->price(),
                $pass->highlight()
            );
        }

        return $rows;
    }

    private function mapHeroImageViewModels(?Section $heroSection): array
    {
        if ($heroSection === null) {
            return [];
        }

        $rows = [];
        foreach ($heroSection->getItemsByCategorie(self::ITEM_CATEGORY_HERO_IMAGE) as $item) {
            if (!$item instanceof SectionItem) {
                continue;
            }

            $rows[] = new JazzDetailHeroImageRowViewModel(
                $item->id,
                trim((string)($item->image ?? '')),
                trim((string)($item->subTitle ?? ''))
            );
        }

        return $rows;
    }

    private function mapHeroImageRequestViewModels(array $heroImages): array
    {
        $rows = [];
        foreach ($heroImages as $image) {
            if (!$image instanceof JazzDetailHeroImageEditRow) {
                continue;
            }

            $rows[] = new JazzDetailHeroImageRowViewModel($image->id(), $image->image(), $image->alt());
        }

        return $rows;
    }

    private function mapHighlightViewModels(?Section $section): array
    {
        if ($section === null) {
            return [];
        }

        $rows = [];
        foreach ($section->getItemsByCategorie(self::ITEM_CATEGORY_HIGHLIGHT) as $item) {
            if (!$item instanceof SectionItem) {
                continue;
            }

            $rows[] = new JazzDetailHighlightRowViewModel(
                $item->id,
                trim((string)($item->icon ?? '')),
                trim($item->title),
                trim((string)($item->content ?? ''))
            );
        }

        return $rows;
    }

    private function mapHighlightRequestViewModels(array $highlights): array
    {
        $rows = [];
        foreach ($highlights as $highlight) {
            if (!$highlight instanceof JazzDetailHighlightEditRow) {
                continue;
            }

            $rows[] = new JazzDetailHighlightRowViewModel(
                $highlight->id(),
                $highlight->icon(),
                $highlight->title(),
                $highlight->content()
            );
        }

        return $rows;
    }

    private function mapTrackViewModels(?Section $section): array
    {
        if ($section === null) {
            return [];
        }

        $rows = [];
        foreach ($section->getItemsByCategorie(self::ITEM_CATEGORY_TRACK) as $item) {
            if (!$item instanceof SectionItem) {
                continue;
            }

            $rows[] = new JazzDetailTrackRowViewModel(
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

    private function mapTrackRequestViewModels(array $tracks): array
    {
        $rows = [];
        foreach ($tracks as $track) {
            if (!$track instanceof JazzDetailTrackEditRow) {
                continue;
            }

            $rows[] = new JazzDetailTrackRowViewModel(
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
