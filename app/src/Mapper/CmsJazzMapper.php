<?php

namespace App\Mapper;

use App\Models\Page\Page;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use App\Models\Edit\Jazz\JazzDetailHeroImageEditRow;
use App\Models\Edit\Jazz\JazzDetailHighlightEditRow;
use App\Models\Edit\Jazz\JazzDetailTrackEditRow;
use App\Models\Edit\Jazz\JazzHomePassEditRow;

class CmsJazzMapper
{
    private const SECTION_SCHEDULE = 'jazz_schedule';
    private const SECTION_ARTISTS = 'jazz_artists';
    private const SECTION_PASSES = 'jazz_passes';
    private const SECTION_DETAIL_HERO = 'jazz_detail_hero';
    private const SECTION_DETAIL_HIGHLIGHTS = 'jazz_detail_highlights';
    private const SECTION_DETAIL_TRACKS = 'jazz_detail_tracks';
    private const SECTION_DETAIL_INFO = 'jazz_detail_info';

    private const ITEM_HIGHLIGHT_FLAG = 'highlight';
    private const ITEM_CATEGORY_PASS = 'pass';
    private const ITEM_CATEGORY_HERO_IMAGE = 'hero_image';
    private const ITEM_CATEGORY_HIGHLIGHT = 'highlight';
    private const ITEM_CATEGORY_TRACK = 'track';
    private const DEFAULT_HIGHLIGHT_ICON = 'star';

    public function mapHomeSectionsForSave(Page $page): array
    {
        $schedule = $page->getSection(self::SECTION_SCHEDULE);
        $artists = $page->getSection(self::SECTION_ARTISTS);
        $passes = $page->getSection(self::SECTION_PASSES);
        
        if ($schedule === null || $artists === null ||  $passes === null) {
            throw new \RuntimeException('Required jazz sections are missing.');
        }

        return [
            $this->mapSectionForSave(self::SECTION_SCHEDULE, $schedule, 5),
            $this->mapSectionForSave(self::SECTION_ARTISTS, $artists, 15),
            $this->mapSectionForSave(self::SECTION_PASSES, $passes, 10, $this->mapPassRows($passes->items)),
        ];
    }

    public function mapDetailSectionsForSave(Page $page): array
    {
        $hero = $page->getSection(self::SECTION_DETAIL_HERO);
        $highlights = $page->getSection(self::SECTION_DETAIL_HIGHLIGHTS);
        $tracks = $page->getSection(self::SECTION_DETAIL_TRACKS);
        $info = $page->getSection(self::SECTION_DETAIL_INFO);

        if ($hero === null || $highlights === null || $tracks === null || $info === null) {
            throw new \RuntimeException('Required Jazz detail sections are missing.');
        }

        return [
            $this->mapSectionForSave(self::SECTION_DETAIL_HERO, $hero, 10, $this->mapHeroImageRows($hero->items)),
            $this->mapSectionForSave(self::SECTION_DETAIL_HIGHLIGHTS, $highlights, 20, $this->mapHighlightRows($highlights->items)),
            $this->mapSectionForSave(self::SECTION_DETAIL_TRACKS, $tracks, 30, $this->mapTrackRows($tracks->items)),
            $this->mapSectionForSave(self::SECTION_DETAIL_INFO, $info, 40),
        ];
    }

    public function normalizePasses(array $passes): array
    {
        $result = [];
        foreach ($passes as $pass) {
            if (!$pass instanceof JazzHomePassEditRow) {
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
                $pass->highlight() ? self::ITEM_HIGHLIGHT_FLAG : null,
                self::ITEM_CATEGORY_PASS,
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
            if (!$image instanceof JazzDetailHeroImageEditRow) {
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

    public function normalizeHighlights(array $highlights): array
    {
        $result = [];
        foreach ($highlights as $highlight) {
            if (!$highlight instanceof JazzDetailHighlightEditRow) {
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

    public function normalizeTracks(array $tracks, array $existingTrackAudioUrls = []): array
    {
        $result = [];
        foreach ($tracks as $track) {
            if (!$track instanceof JazzDetailTrackEditRow) {
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
            $highlight = ($pass->url ?? '') === self::ITEM_HIGHLIGHT_FLAG;
            if ($label === '' || $price === '') {
                continue;
            }

            $rows[] = [
                'id' => $pass->id,
                'title' => $label,
                'item_subtitle' => null,
                'content' => $price,
                'image_path' => null,
                'link_url' => $highlight ? self::ITEM_HIGHLIGHT_FLAG : null,
                'duration' => null,
                'icon_class' => null,
                'order_index' => $index++,
                'item_category' => self::ITEM_CATEGORY_PASS,
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
                'item_category' => self::ITEM_CATEGORY_HERO_IMAGE,
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
                'icon_class' => trim((string)($highlight->icon ?? '')) ?: self::DEFAULT_HIGHLIGHT_ICON,
                'order_index' => $index++,
                'item_category' => self::ITEM_CATEGORY_HIGHLIGHT,
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

    private function mapSectionForSave(string $sectionType, Section $section, int $orderIndex, array $items = []): array
    {
        $subtitle = $this->sectionUsesSubtitle($sectionType) ? $section->subTitle : null;
        $description = $this->sectionUsesDescription($sectionType) ? $section->description : null;

        return [
            'type' => $sectionType,
            'title' => $section->title,
            'subtitle' => $subtitle,
            'description' => $description,
            'order_index' => $orderIndex,
            'items' => $items,
        ];
    }

    private function sectionUsesSubtitle(string $sectionType): bool
    {
        return $sectionType !== self::SECTION_SCHEDULE
            && $sectionType !== self::SECTION_ARTISTS
            && $sectionType !== self::SECTION_PASSES
            && $sectionType !== self::SECTION_DETAIL_HIGHLIGHTS
            && $sectionType !== self::SECTION_DETAIL_INFO;
    }

    private function sectionUsesDescription(string $sectionType): bool
    {
        return $sectionType !== self::SECTION_SCHEDULE
            && $sectionType !== self::SECTION_ARTISTS
            && $sectionType !== self::SECTION_PASSES
            && $sectionType !== self::SECTION_DETAIL_HIGHLIGHTS;
    }
}
