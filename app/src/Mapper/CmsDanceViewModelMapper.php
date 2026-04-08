<?php

namespace App\Mapper;

use App\Models\Dance\DanceDetailEditorData;
use App\Models\Event\EventDetailPageModel;
use App\Models\Page\Page;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use App\Models\Edit\Dance\DanceDetailHeroImageEditRow;
use App\Models\Edit\Dance\DanceDetailHighlightEditRow;
use App\Models\Edit\Dance\DanceDetailTrackEditRow;
use App\Models\Requests\UpdateDanceDetailRequest;
use App\Models\Requests\UpdateDanceHomeRequest;
use App\Models\ViewModels\Cms\Dance\DanceDetailEditViewModel;
use App\Models\ViewModels\Cms\Dance\DanceDetailHeroImageRowViewModel;
use App\Models\ViewModels\Cms\Dance\DanceDetailHighlightRowViewModel;
use App\Models\ViewModels\Cms\Dance\DanceDetailTrackRowViewModel;
use App\Models\ViewModels\Cms\Dance\DanceHomeEditViewModel;

class CmsDanceViewModelMapper
{
    private const SECTION_SCHEDULE = 'dance_schedule';
    private const SECTION_ARTISTS = 'dance_artists';
    private const SECTION_BANNER = 'dance_banner';
    private const SECTION_INFO = 'dance_info';
    private const SECTION_PASSES = 'dance_passes';
    private const SECTION_CAPACITY = 'dance_capacity';
    private const SECTION_SPECIAL = 'dance_special_session';
    private const SECTION_DETAIL_HERO = 'dance_detail_hero';
    private const SECTION_DETAIL_HIGHLIGHTS = 'dance_detail_highlights';
    private const SECTION_DETAIL_TRACKS = 'dance_detail_tracks';
    private const SECTION_DETAIL_SCHEDULE = 'dance_detail_schedule';
    private const SECTION_DETAIL_INFO = 'dance_detail_info';
    private const ITEM_CATEGORY_HERO_IMAGE = 'hero_image';
    private const ITEM_CATEGORY_HIGHLIGHT = 'highlight';
    private const ITEM_CATEGORY_TRACK = 'track';

    // Maps the stored dance home page into a CMS edit view model so the editor can preload existing values.
    public function mapHomePageToEditViewModel(Page $page): DanceHomeEditViewModel
    {
        $schedule = $page->getSection(self::SECTION_SCHEDULE);
        $artists = $page->getSection(self::SECTION_ARTISTS);
        $banner = $page->getSection(self::SECTION_BANNER);
        $info = $page->getSection(self::SECTION_INFO);
        $passes = $page->getSection(self::SECTION_PASSES);
        $capacity = $page->getSection(self::SECTION_CAPACITY);
        $special = $page->getSection(self::SECTION_SPECIAL);

        return new DanceHomeEditViewModel(
            $page->title,
            $schedule !== null ? $schedule->title : '',
            $artists !== null ? $artists->title : '',
            $banner !== null ? (string)$banner->subTitle : '',
            $banner !== null ? $banner->title : '',
            $banner !== null ? (string)$banner->description : '',
            $info !== null ? $info->title : '',
            $info !== null ? (string)$info->description : '',
            $passes !== null ? $passes->title : '',
            $capacity !== null ? $capacity->title : '',
            $capacity !== null ? (string)$capacity->description : '',
            $special !== null ? $special->title : '',
            $special !== null ? (string)$special->description : ''
        );
    }

    // Maps a posted dance home request back into a CMS edit view model so validation errors can re-render submitted values.
    public function mapHomeRequestToEditViewModel(UpdateDanceHomeRequest $request): DanceHomeEditViewModel
    {
        return new DanceHomeEditViewModel(
            $request->pageTitle(),
            $request->scheduleTitle(),
            $request->featuredArtistsTitle(),
            $request->bannerBadge(),
            $request->bannerTitle(),
            $request->bannerDescription(),
            $request->importantInformationTitle(),
            $request->importantInformationHtml(),
            $request->passesTitle(),
            $request->capacityTitle(),
            $request->capacityHtml(),
            $request->specialTitle(),
            $request->specialHtml()
        );
    }

    // Maps CMS dance detail data into an edit view model so the editor gets page content, labels, and media rows together.
    public function mapDetailDataToEditViewModel(DanceDetailEditorData $detailData): DanceDetailEditViewModel
    {
        $meta = $detailData->detailMeta;
        $page = $detailData->contentPage;
        $editorTitle = $detailData->editorTitle;
        $performerName = $detailData->performerName;
        $hero = $page->getSection(self::SECTION_DETAIL_HERO);
        $highlights = $page->getSection(self::SECTION_DETAIL_HIGHLIGHTS);
        $tracks = $page->getSection(self::SECTION_DETAIL_TRACKS);
        $schedule = $page->getSection(self::SECTION_DETAIL_SCHEDULE);
        $info = $page->getSection(self::SECTION_DETAIL_INFO);

        return new DanceDetailEditViewModel(
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
            $schedule !== null ? $schedule->title : 'DANCE! Festival Schedule',
            $info !== null ? $info->title : '',
            $info !== null ? (string)$info->description : ''
        );
    }

    // Maps a posted dance detail request back into an edit view model so validation errors can re-render submitted values.
    public function mapDetailRequestToEditViewModel(UpdateDanceDetailRequest $request, DanceDetailEditViewModel $baseViewModel): DanceDetailEditViewModel
    {
        return new DanceDetailEditViewModel(
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
            $request->scheduleTitle(),
            $request->importantInformationTitle(),
            $request->importantInformationHtml()
        );
    }

    // Maps stored hero image items into CMS hero image rows so the detail editor can show existing images and alt text.
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

            $rows[] = new DanceDetailHeroImageRowViewModel(
                $item->id,
                trim((string)($item->image ?? '')),
                trim((string)($item->subTitle ?? ''))
            );
        }

        return $rows;
    }

    // Maps posted hero image edit rows into CMS hero image rows so failed saves preserve submitted media values.
    private function mapHeroImageRequestViewModels(array $heroImages): array
    {
        $rows = [];
        foreach ($heroImages as $image) {
            if (!$image instanceof DanceDetailHeroImageEditRow) {
                continue;
            }

            $rows[] = new DanceDetailHeroImageRowViewModel($image->id(), $image->image(), $image->alt());
        }

        return $rows;
    }

    // Maps stored highlight items into CMS highlight rows so the detail editor can show existing highlight content.
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

            $rows[] = new DanceDetailHighlightRowViewModel(
                $item->id,
                trim((string)($item->icon ?? '')),
                trim($item->title),
                trim((string)($item->content ?? ''))
            );
        }

        return $rows;
    }

    // Maps posted highlight edit rows into CMS highlight rows so failed saves preserve submitted highlight values.
    private function mapHighlightRequestViewModels(array $highlights): array
    {
        $rows = [];
        foreach ($highlights as $highlight) {
            if (!$highlight instanceof DanceDetailHighlightEditRow) {
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

    // Maps stored track items into CMS track rows so the detail editor can show existing track metadata and media.
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

    // Maps posted track edit rows into CMS track rows so failed saves preserve submitted track values.
    private function mapTrackRequestViewModels(array $tracks): array
    {
        $rows = [];
        foreach ($tracks as $track) {
            if (!$track instanceof DanceDetailTrackEditRow) {
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
