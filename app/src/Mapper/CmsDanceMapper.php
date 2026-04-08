<?php

namespace App\Mapper;

use App\Models\Page\SectionItem;
use App\Models\Edit\Dance\DanceDetailHeroImageEditRow;
use App\Models\Edit\Dance\DanceDetailHighlightEditRow;
use App\Models\Edit\Dance\DanceDetailTrackEditRow;
use App\Models\Requests\UpdateDanceDetailRequest;
use App\Models\Requests\UpdateDanceHomeRequest;

class CmsDanceMapper
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
    private const DEFAULT_HIGHLIGHT_ICON = 'star';

    // Builds the dance home save payload directly from the CMS request so saves do not need a temporary Page object.
    public function mapHomeRequestSectionsForSave(
        UpdateDanceHomeRequest $request,
        string $bannerDescription,
        string $importantInformationHtml,
        string $capacityHtml,
        string $specialHtml
    ): array {
        return [
            $this->createSectionPayload(self::SECTION_SCHEDULE, $request->scheduleTitle(), null, null, 5),
            $this->createSectionPayload(self::SECTION_BANNER, $request->bannerTitle(), $request->bannerBadge(), $bannerDescription, 10),
            $this->createSectionPayload(self::SECTION_ARTISTS, $request->featuredArtistsTitle(), null, null, 15),
            $this->createSectionPayload(self::SECTION_INFO, $request->importantInformationTitle(), null, $importantInformationHtml, 20),
            $this->createSectionPayload(self::SECTION_PASSES, $request->passesTitle(), null, null, 40),
            $this->createSectionPayload(self::SECTION_CAPACITY, $request->capacityTitle(), null, $capacityHtml, 50),
            $this->createSectionPayload(self::SECTION_SPECIAL, $request->specialTitle(), null, $specialHtml, 60),
        ];
    }

    // Builds the dance detail save payload directly from the CMS request so detail saves can persist normalized sections in one pass.
    public function mapDetailRequestSectionsForSave(
        UpdateDanceDetailRequest $request,
        array $heroImages,
        array $highlights,
        array $tracks,
        string $importantInformationHtml
    ): array {
        return [
            $this->createSectionPayload(
                self::SECTION_DETAIL_HERO,
                $request->heroTitle(),
                $request->heroBadge(),
                $request->heroSubtitle(),
                10,
                $this->mapHeroImageRows($heroImages)
            ),
            $this->createSectionPayload(
                self::SECTION_DETAIL_HIGHLIGHTS,
                $request->highlightsTitle(),
                null,
                null,
                20,
                $this->mapHighlightRows($highlights)
            ),
            $this->createSectionPayload(
                self::SECTION_DETAIL_TRACKS,
                $request->tracksTitle(),
                null,
                $request->tracksNote(),
                30,
                $this->mapTrackRows($tracks)
            ),
            $this->createSectionPayload(
                self::SECTION_DETAIL_SCHEDULE,
                $request->scheduleTitle(),
                null,
                null,
                35
            ),
            $this->createSectionPayload(
                self::SECTION_DETAIL_INFO,
                $request->importantInformationTitle(),
                null,
                $importantInformationHtml,
                40
            ),
        ];
    }

    // Normalizes posted hero image rows into section items so detail page media can be saved in order.
    public function normalizeHeroImages(array $heroImages): array
    {
        $result = [];
        foreach ($heroImages as $image) {
            if (!$image instanceof DanceDetailHeroImageEditRow) {
                continue;
            }

            $result[] = new SectionItem(
                $image->id(),
                '',
                null,
                $image->image(),
                null,
                self::ITEM_CATEGORY_HERO_IMAGE,
                null,
                null,
                $image->alt(),
                count($result) + 1
            );
        }

        return $result;
    }

    // Normalizes posted highlight rows into section items so empty rows are ignored before validation and save.
    public function normalizeHighlights(array $highlights): array
    {
        $result = [];
        foreach ($highlights as $highlight) {
            if (!$highlight instanceof DanceDetailHighlightEditRow) {
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
                self::ITEM_CATEGORY_HIGHLIGHT,
                null,
                $highlight->icon() !== '' ? $highlight->icon() : self::DEFAULT_HIGHLIGHT_ICON,
                null,
                count($result) + 1
            );
        }

        return $result;
    }

    // Normalizes posted track rows into section items and preserves existing audio URLs so unchanged tracks keep their media.
    public function normalizeTracks(array $tracks, array $existingTrackAudioUrls = []): array
    {
        $result = [];
        foreach ($tracks as $track) {
            if (!$track instanceof DanceDetailTrackEditRow) {
                continue;
            }

            $audioUrl = $track->audioUrl();
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
                self::ITEM_CATEGORY_TRACK,
                null,
                null,
                $track->subtitle(),
                count($result) + 1
            );
        }

        return $result;
    }

    // Converts normalized hero image items into repository save rows so hero media persists with alt text and order.
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
                'item_category' => self::ITEM_CATEGORY_HERO_IMAGE,
            ];
        }

        return $rows;
    }

    // Converts normalized highlight items into repository save rows so icons and text persist in CMS order.
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
                'icon_class' => trim((string)($highlight->icon ?? '')) ?: self::DEFAULT_HIGHLIGHT_ICON,
                'order_index' => $index++,
                'item_category' => self::ITEM_CATEGORY_HIGHLIGHT,
            ];
        }

        return $rows;
    }

    // Converts normalized track items into repository save rows so image, subtitle, year, and audio path persist together.
    public function mapTrackRows(array $tracks): array
    {
        $rows = [];
        $index = 1;

        foreach ($tracks as $track) {
            if (!$track instanceof SectionItem) {
                continue;
            }

            $linkUrl = trim((string)($track->url ?? ''));

            $rows[] = [
                'id' => $track->id,
                'title' => trim($track->title),
                'item_subtitle' => trim((string)($track->subTitle ?? '')),
                'content' => trim((string)($track->content ?? '')),
                'image_path' => trim((string)($track->image ?? '')),
                'link_url' => $linkUrl !== '' ? $linkUrl : null,
                'duration' => null,
                'icon_class' => null,
                'order_index' => $index++,
                'item_category' => self::ITEM_CATEGORY_TRACK,
            ];
        }

        return $rows;
    }

    // Builds one section payload so request-driven saves share one consistent output shape for the page save service.
    private function createSectionPayload(
        string $sectionType,
        string $title,
        ?string $subtitle,
        ?string $description,
        int $orderIndex,
        array $items = []
    ): array {
        return [
            'type' => $sectionType,
            'title' => $title,
            'subtitle' => $this->sectionUsesSubtitle($sectionType) ? $subtitle : null,
            'description' => $this->sectionUsesDescription($sectionType) ? $description : null,
            'order_index' => $orderIndex,
            'items' => $items,
        ];
    }

    // Flags which section types actually store subtitles so unsupported fields are not written to storage.
    private function sectionUsesSubtitle(string $sectionType): bool
    {
        return $sectionType !== self::SECTION_SCHEDULE
            && $sectionType !== self::SECTION_ARTISTS
            && $sectionType !== self::SECTION_INFO
            && $sectionType !== self::SECTION_PASSES
            && $sectionType !== self::SECTION_CAPACITY
            && $sectionType !== self::SECTION_SPECIAL
            && $sectionType !== self::SECTION_DETAIL_HIGHLIGHTS
            && $sectionType !== self::SECTION_DETAIL_SCHEDULE
            && $sectionType !== self::SECTION_DETAIL_INFO;
    }

    // Flags which section types actually store descriptions so unsupported fields are omitted from save payloads.
    private function sectionUsesDescription(string $sectionType): bool
    {
        return $sectionType !== self::SECTION_SCHEDULE
            && $sectionType !== self::SECTION_ARTISTS
            && $sectionType !== self::SECTION_PASSES
            && $sectionType !== self::SECTION_DETAIL_SCHEDULE
            && $sectionType !== self::SECTION_DETAIL_HIGHLIGHTS;
    }
}
