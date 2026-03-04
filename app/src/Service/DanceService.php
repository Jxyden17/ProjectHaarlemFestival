<?php

namespace App\Service;

use App\Models\Commands\Cms\Dance\DanceHomeSaveCommand;
use App\Models\Page\Page;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use App\Models\Event\PerformerModel;
use App\Models\Requests\Cms\Dance\DanceHomeArtistRowRequest;
use App\Models\Requests\Cms\Dance\DanceHomePassRowRequest;
use App\Models\ViewModels\Cms\Dance\DanceHomeArtistRowViewModel;
use App\Models\ViewModels\Cms\Dance\DanceHomeContentViewModel;
use App\Models\ViewModels\Cms\Dance\DanceHomePassRowViewModel;
use App\Models\ViewModels\Dance\DanceBannerStatsViewModel;
use App\Repository\Interfaces\IDanceRepository;
use App\Repository\Interfaces\IPageRepository;
use App\Service\Interfaces\IDanceService;
use App\Service\Interfaces\IHtmlSanitizerService;

class DanceService implements IDanceService
{
    private IDanceRepository $danceRepository;
    private IPageRepository $pageRepository;
    private IHtmlSanitizerService $htmlSanitizer;

    public function __construct(IDanceRepository $danceRepository, IPageRepository $pageRepository, IHtmlSanitizerService $htmlSanitizer)
    {
        $this->danceRepository = $danceRepository;
        $this->pageRepository = $pageRepository;
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

    public function getDancePerformers(): array
    {
        $event = $this->danceRepository->findDanceEvent();

        if ($event === null) {
            return [];
        }

        return $this->danceRepository->getPerformersByEventId($event->id);
    }

    public function getDanceHomePage(): Page
    {
        return $this->pageRepository->getPageBySlug('dance-home', 'Dance Home');
    }

    public function getDanceHomeFormData(): DanceHomeContentViewModel
    {
        $page = $this->getDanceHomePage();
        $schedule = $page->getSection('dance_schedule');
        $banner = $page->getSection('dance_banner');
        $artists = $page->getSection('dance_artists');
        $info = $page->getSection('dance_info');
        $passes = $page->getSection('dance_passes');
        $capacity = $page->getSection('dance_capacity');
        $special = $page->getSection('dance_special_session');

        $artistRows = [];
        $performers = $this->getDancePerformers();
        $artistImageRows = [];
        if ($artists !== null) {
            foreach ($artists->getItemsByCategorie('artist') as $item) {
                if ($item instanceof SectionItem) {
                    $artistImageRows[] = $item;
                }
            }
        }

        foreach ($performers as $index => $performer) {
            if (!$performer instanceof PerformerModel) {
                continue;
            }

            $imageRow = $artistImageRows[$index] ?? null;
            if (!$imageRow instanceof SectionItem) {
                continue;
            }

            $artistRows[] = new DanceHomeArtistRowViewModel(
                $imageRow->id,
                $performer->performerName,
                (string)($performer->performerType ?? ''),
                (string)($imageRow->image ?? '')
            );
        }

        $passRows = [];
        if ($passes !== null) {
            foreach ($passes->getItemsByCategorie('pass') as $item) {
                if (!$item instanceof SectionItem) {
                    continue;
                }

                $passRows[] = new DanceHomePassRowViewModel(
                    $item->id,
                    $item->title,
                    (string)($item->content ?? ''),
                    (string)($item->url ?? '') === 'highlight'
                );
            }
        }

        return new DanceHomeContentViewModel(
            $schedule !== null ? $schedule->title : '',
            $banner !== null ? (string)$banner->subTitle : '',
            $banner !== null ? $banner->title : '',
            $banner !== null ? (string)$banner->description : '',
            $artists !== null ? $artists->title : '',
            $artistRows,
            $info !== null ? $info->title : '',
            $info !== null ? (string)$info->description : '',
            $passes !== null ? $passes->title : '',
            $passRows,
            $capacity !== null ? $capacity->title : '',
            $capacity !== null ? (string)$capacity->description : '',
            $special !== null ? $special->title : '',
            $special !== null ? (string)$special->description : ''
        );
    }

    public function saveDanceHomePage(DanceHomeSaveCommand $command): void
    {
        $normalizedInput = $this->normalizeHomePageInput($command);
        $normalizedInput['artist_items'] = $this->synchronizeArtistItemsWithPerformers($normalizedInput['artist_items']);
        $this->validateHomePageInput($normalizedInput);
        $page = $this->buildDanceHomePage($normalizedInput);
        $this->persistDanceHomePage($page);
    }

    private function normalizeHomePageInput(DanceHomeSaveCommand $command): array
    {
        $artists = $command->artists();
        $passes = $command->passes();

        return [
            'schedule_title' => trim($command->scheduleTitle()),
            'banner_badge' => trim($command->bannerBadge()),
            'banner_title' => trim($command->bannerTitle()),
            'banner_description' => trim($command->bannerDescription()),
            'artists_title' => trim($command->artistsTitle()),
            'artist_items' => $this->normalizeArtists($artists),
            'important_information_title' => trim($command->importantInformationTitle()),
            'important_information_html' => $this->sanitizeWysiwygField($command->importantInformationHtml()),
            'passes_title' => trim($command->passesTitle()),
            'pass_items' => $this->normalizePasses($passes),
            'capacity_title' => trim($command->capacityTitle()),
            'capacity_html' => $this->sanitizeWysiwygField($command->capacityHtml()),
            'special_title' => trim($command->specialTitle()),
            'special_html' => $this->sanitizeWysiwygField($command->specialHtml()),
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
            if (!$artist instanceof DanceHomeArtistRowRequest) {
                continue;
            }

            $name = trim($artist->name());
            $genre = trim($artist->genre());
            $image = trim($artist->image());

            if ($name === '') {
                continue;
            }

            $id = $artist->id();
            $result[] = new SectionItem($id, $name, $genre, $image, null, 'artist', null, null, null, count($result) + 1);
        }

        return $result;
    }

    private function synchronizeArtistItemsWithPerformers(array $artistItems): array
    {
        $performers = $this->getDancePerformers();
        $synced = [];

        foreach ($performers as $index => $performer) {
            if (!$performer instanceof PerformerModel) {
                continue;
            }

            $existing = $artistItems[$index] ?? null;
            $existingId = $existing instanceof SectionItem ? (int)$existing->id : 0;
            $existingImage = $existing instanceof SectionItem ? trim((string)($existing->image ?? '')) : '';
            if ($existingId <= 0) {
                continue;
            }

            $synced[] = new SectionItem(
                $existingId,
                $performer->performerName,
                trim((string)($performer->performerType ?? '')),
                $existingImage,
                null,
                'artist',
                null,
                null,
                null,
                count($synced) + 1
            );
        }

        return $synced;
    }

    private function normalizePasses(array $passes): array
    {
        $result = [];
        foreach ($passes as $pass) {
            if (!$pass instanceof DanceHomePassRowRequest) {
                continue;
            }

            $label = trim($pass->label());
            $price = trim($pass->price());
            if ($label === '' || $price === '') {
                continue;
            }

            $id = $pass->id();
            $result[] = new SectionItem($id, $label, $price, null, $pass->highlight() ? 'highlight' : null, 'pass', null, null, null, count($result) + 1);
        }

        return $result;
    }

    private function persistDanceHomePage(Page $page): void
    {
        $event = $this->danceRepository->findDanceEvent();
        if ($event === null) {
            throw new \RuntimeException('Dance event not found.');
        }

        $pageId = $this->pageRepository->ensurePageBySlug((int)$event->id, 'dance-home', 'Dance Home');

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

        $this->pageRepository->saveOrUpdateSection($pageId, 'dance_schedule', $scheduleSection->title, null, null, 5);
        $this->pageRepository->saveOrUpdateSection($pageId, 'dance_banner', $bannerSection->title, $bannerSection->subTitle, $bannerSection->description, 10);

        $this->pageRepository->saveOrUpdateSection($pageId, 'dance_info', $infoSection->title, null, $infoSection->description, 20);

        $artistsSectionId = $this->pageRepository->saveOrUpdateSection($pageId, 'dance_artists', $artistsSection->title, null, null, 30);
        $this->pageRepository->upsertSectionItems($artistsSectionId, $this->mapArtistRows($artistsSection->items));

        $passesSectionId = $this->pageRepository->saveOrUpdateSection($pageId, 'dance_passes', $passesSection->title, null, null, 40);
        $this->pageRepository->upsertSectionItems($passesSectionId, $this->mapPassRows($passesSection->items));

        $this->pageRepository->saveOrUpdateSection($pageId, 'dance_capacity', $capacitySection->title, null, $capacitySection->description, 50);
        $this->pageRepository->saveOrUpdateSection($pageId, 'dance_special_session', $specialSection->title, null, $specialSection->description, 60);
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
                'id' => $artist->id,
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
                'id' => $pass->id,
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
