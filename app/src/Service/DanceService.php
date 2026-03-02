<?php

namespace App\Service;

use App\Models\Page\Page;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use App\Models\ViewModels\Dance\DanceBannerStatsViewModel;
use App\Repository\DanceRepository;
use App\Service\Interfaces\IDanceService;

class DanceService implements IDanceService
{
    private DanceRepository $danceRepository;
    private HtmlSanitizerService $htmlSanitizer;

    public function __construct(DanceRepository $danceRepository, HtmlSanitizerService $htmlSanitizer)
    {
        $this->danceRepository = $danceRepository;
        $this->htmlSanitizer = $htmlSanitizer;
    }

    public function getDanceBannerStats(): DanceBannerStatsViewModel
    {
        $event = $this->danceRepository->findDanceEvent();

        if ($event === null) {
            throw new \RuntimeException('Dance event not found.');
        }

        return new DanceBannerStatsViewModel(
            $this->danceRepository->countSessionsByEventId($event->id),
            $this->danceRepository->countDistinctVenuesByEventId($event->id)
        );
    }

    public function getDanceVenues(): array
    {
        $event = $this->danceRepository->findDanceEvent();

        if ($event === null) {
            return [];
        }

        return $this->danceRepository->getVenuesByEventId($event->id);
    }

    public function getDanceHomePage(): Page
    {
        return $this->danceRepository->getDanceHomePage();
    }

    public function saveDanceHomePage(array $input): void
    {
        $normalizedInput = $this->normalizeHomePageInput($input);
        $this->validateHomePageInput($normalizedInput);
        $page = $this->buildDanceHomePage($normalizedInput);
        $this->persistDanceHomePage($page);
    }

    private function normalizeHomePageInput(array $input): array
    {
        $artists = is_array($input['artists'] ?? null) ? $input['artists'] : [];
        $passes = is_array($input['passes'] ?? null) ? $input['passes'] : [];

        return [
            'schedule_title' => trim((string)($input['schedule_title'] ?? '')),
            'banner_badge' => trim((string)($input['banner_badge'] ?? '')),
            'banner_title' => trim((string)($input['banner_title'] ?? '')),
            'banner_description' => trim((string)($input['banner_description'] ?? '')),
            'artists_title' => trim((string)($input['artists_title'] ?? '')),
            'artist_items' => $this->normalizeArtists($artists),
            'important_information_title' => trim((string)($input['important_information_title'] ?? '')),
            'important_information_html' => $this->sanitizeWysiwygField((string)($input['important_information_html'] ?? '')),
            'passes_title' => trim((string)($input['passes_title'] ?? '')),
            'pass_items' => $this->normalizePasses($passes),
            'capacity_title' => trim((string)($input['capacity_title'] ?? '')),
            'capacity_html' => $this->sanitizeWysiwygField((string)($input['capacity_html'] ?? '')),
            'special_title' => trim((string)($input['special_title'] ?? '')),
            'special_html' => $this->sanitizeWysiwygField((string)($input['special_html'] ?? '')),
        ];
    }

    private function sanitizeWysiwygField(string $value): string
    {
        return $this->htmlSanitizer->sanitizeWysiwygHtml($value);
    }

    private function validateHomePageInput(array $input): void
    {
        if ($input['schedule_title'] === '') {
            throw new \InvalidArgumentException('Schedule title is required.');
        }

        if ($input['banner_title'] === '') {
            throw new \InvalidArgumentException('Banner title is required.');
        }

        if ($input['banner_description'] === '') {
            throw new \InvalidArgumentException('Banner description is required.');
        }

        if ($input['artists_title'] === '' || count($input['artist_items']) === 0) {
            throw new \InvalidArgumentException('At least one artist is required.');
        }

        if ($input['important_information_title'] === '' || $input['important_information_html'] === '') {
            throw new \InvalidArgumentException('Important information is required.');
        }

        if ($input['passes_title'] === '' || count($input['pass_items']) === 0) {
            throw new \InvalidArgumentException('At least one pass row is required.');
        }

        if ($input['capacity_title'] === '' || $input['capacity_html'] === '') {
            throw new \InvalidArgumentException('Capacity content is required.');
        }

        if ($input['special_title'] === '' || $input['special_html'] === '') {
            throw new \InvalidArgumentException('Special session content is required.');
        }
    }

    private function buildDanceHomePage(array $input): Page
    {
        $page = new Page('Dance Home', 'dance-home');
        $page->sections = [
            new Section(0, 'dance_schedule', $input['schedule_title'], '', ''),
            new Section(0, 'dance_banner', $input['banner_title'], $input['banner_badge'], $input['banner_description']),
            new Section(0, 'dance_info', $input['important_information_title'], '', $input['important_information_html']),
            new Section(0, 'dance_artists', $input['artists_title'], '', ''),
            new Section(0, 'dance_passes', $input['passes_title'], '', ''),
            new Section(0, 'dance_capacity', $input['capacity_title'], '', $input['capacity_html']),
            new Section(0, 'dance_special_session', $input['special_title'], '', $input['special_html']),
        ];

        $this->appendSectionItems($page, 'dance_artists', $input['artist_items']);
        $this->appendSectionItems($page, 'dance_passes', $input['pass_items']);

        return $page;
    }

    private function appendSectionItems(Page $page, string $sectionType, array $items): void
    {
        $section = $page->getSection($sectionType);
        if ($section === null) {
            return;
        }

        foreach ($items as $item) {
            if ($item instanceof SectionItem) {
                $section->addItem($item);
            }
        }
    }

    private function normalizeArtists(array $artists): array
    {
        $result = [];
        foreach ($artists as $artist) {
            if (!is_array($artist)) {
                continue;
            }

            $name = trim((string)($artist['name'] ?? ''));
            $genre = trim((string)($artist['genre'] ?? ''));
            $image = trim((string)($artist['image'] ?? ''));

            if ($name === '') {
                continue;
            }

            $result[] = new SectionItem(0, $name, $genre, $image, null, 'artist', null, null, null, count($result) + 1);
        }

        return $result;
    }

    private function normalizePasses(array $passes): array
    {
        $result = [];
        foreach ($passes as $pass) {
            if (!is_array($pass)) {
                continue;
            }

            $label = trim((string)($pass['label'] ?? ''));
            $price = trim((string)($pass['price'] ?? ''));
            if ($label === '' || $price === '') {
                continue;
            }

            $result[] = new SectionItem(0, $label, $price, null, !empty($pass['highlight']) ? 'highlight' : null, 'pass', null, null, null, count($result) + 1);
        }

        return $result;
    }

    private function persistDanceHomePage(Page $page): void
    {
        $event = $this->danceRepository->findDanceEvent();
        if ($event === null) {
            throw new \RuntimeException('Dance event not found.');
        }

        $pageId = $this->danceRepository->ensureDanceHomePage((int)$event->id);

        $scheduleSection = $page->getSection('dance_schedule');
        $bannerSection = $page->getSection('dance_banner');
        $infoSection = $page->getSection('dance_info');
        $artistsSection = $page->getSection('dance_artists');
        $passesSection = $page->getSection('dance_passes');
        $capacitySection = $page->getSection('dance_capacity');
        $specialSection = $page->getSection('dance_special_session');

        if ($scheduleSection === null || $bannerSection === null || $infoSection === null || $artistsSection === null || $passesSection === null || $capacitySection === null || $specialSection === null) {
            throw new \RuntimeException('Required dance sections are missing.');
        }

        $this->danceRepository->saveOrUpdateSection($pageId, 'dance_schedule', $scheduleSection->title, null, null, 5);
        $this->danceRepository->saveOrUpdateSection($pageId, 'dance_banner', $bannerSection->title, $bannerSection->subTitle, $bannerSection->description, 10);

        $infoSectionId = $this->danceRepository->saveOrUpdateSection($pageId, 'dance_info', $infoSection->title, null, $infoSection->description, 20);

        $artistsSectionId = $this->danceRepository->saveOrUpdateSection($pageId, 'dance_artists', $artistsSection->title, null, null, 30);
        $this->danceRepository->replaceSectionItems($artistsSectionId, $this->mapArtistRows($artistsSection->items));

        $passesSectionId = $this->danceRepository->saveOrUpdateSection($pageId, 'dance_passes', $passesSection->title, null, null, 40);
        $this->danceRepository->replaceSectionItems($passesSectionId, $this->mapPassRows($passesSection->items));

        $this->danceRepository->saveOrUpdateSection($pageId, 'dance_capacity', $capacitySection->title, null, $capacitySection->description, 50);
        $this->danceRepository->saveOrUpdateSection($pageId, 'dance_special_session', $specialSection->title, null, $specialSection->description, 60);

        $this->danceRepository->replaceSectionItems($infoSectionId, []);
    }

    private function mapArtistRows(array $artists): array
    {
        $rows = [];
        $index = 1;

        foreach ($artists as $artist) {
            if (!$artist instanceof SectionItem) {
                continue;
            }

            $name = trim($artist->title);
            $genre = trim((string)($artist->content ?? ''));
            $image = trim((string)($artist->image ?? ''));

            if ($name === '') {
                continue;
            }

            $rows[] = [
                'title' => $name,
                'content' => $genre,
                'image_path' => $image,
                'link_url' => null,
                'order_index' => $index++,
                'item_category' => 'artist',
            ];
        }

        return $rows;
    }

    private function mapPassRows(array $passes): array
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
                'title' => $label,
                'content' => $price,
                'image_path' => null,
                'link_url' => $highlight ? 'highlight' : null,
                'order_index' => $index++,
                'item_category' => 'pass',
            ];
        }

        return $rows;
    }

}
