<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Models\Requests\Cms\Dance\DanceDetailHeroImageRowRequest;
use App\Models\Requests\Cms\Dance\DanceDetailHighlightRowRequest;
use App\Models\Requests\Cms\Dance\DanceDetailTrackRowRequest;
use App\Models\Requests\Cms\Dance\DanceHomeArtistRowRequest;
use App\Models\Requests\Cms\Dance\DanceHomePassRowRequest;
use App\Models\Requests\Cms\DanceDetailContentRequest;
use App\Models\Requests\Cms\DanceHomeContentRequest;
use App\Models\ViewModels\Cms\Dance\DanceDetailContentViewModel;
use App\Models\ViewModels\Cms\Dance\DanceDetailHeroImageRowViewModel;
use App\Models\ViewModels\Cms\Dance\DanceDetailHighlightRowViewModel;
use App\Models\ViewModels\Cms\Dance\DanceDetailTrackRowViewModel;
use App\Models\ViewModels\Cms\Dance\DanceHomeArtistRowViewModel;
use App\Models\ViewModels\Cms\Dance\DanceHomeContentViewModel;
use App\Models\ViewModels\Cms\Dance\DanceHomePassRowViewModel;
use App\Service\Interfaces\ICmsDanceService;

class CmsDanceContentController extends BaseController
{
    private ICmsDanceService $danceService;

    public function __construct(ICmsDanceService $danceService)
    {
        $this->danceService = $danceService;
    }

    public function index(): void
    {
        $this->requireAdmin();
        $contentViewModel = $this->danceService->getDanceHomeFormData();

        $this->renderCms('cms/events/dance-home', [
            'title' => 'Dance Home Content',
            'contentViewModel' => $contentViewModel,
            'success' => isset($_GET['saved']),
        ]);
    }

    public function update(): void
    {
        $this->requireAdmin();
        $request = DanceHomeContentRequest::fromArray($_POST);

        try {
            $this->danceService->saveDanceHomePage($request->toSaveCommand());
            header('Location: /cms/events/dance-home?saved=1');
            exit;
        } catch (\Throwable $e) {
            $contentViewModel = $this->mapHomePostToFormData($request);
            $this->renderCms('cms/events/dance-home', [
                'title' => 'Dance Home Content',
                'contentViewModel' => $contentViewModel,
                'error' => $e->getMessage(),
                'success' => false,
            ]);
        }
    }

    public function detail(array $vars = []): void
    {
        $this->requireAdmin();

        $detailSlug = trim((string)($vars['detailSlug'] ?? ''));
        $contentViewModel = $this->danceService->getDanceDetailFormData($detailSlug);

        $this->renderCms('cms/events/dance-detail', [
            'title' => $contentViewModel->editorTitle,
            'contentViewModel' => $contentViewModel,
            'formAction' => '/cms/events/dance-detail/' . $contentViewModel->detailSlug,
            'success' => isset($_GET['saved']),
        ]);
    }

    public function updateDetail(array $vars = []): void
    {
        $this->requireAdmin();

        $detailSlug = trim((string)($vars['detailSlug'] ?? ''));
        $request = DanceDetailContentRequest::fromArray($_POST);

        try {
            $this->danceService->saveDanceDetailPage($detailSlug, $request->toSaveCommand());
            header('Location: /cms/events/dance-detail/' . rawurlencode($detailSlug) . '?saved=1');
            exit;
        } catch (\Throwable $e) {
            $contentViewModel = $this->mapDetailPostToFormData($detailSlug, $request);
            $this->renderCms('cms/events/dance-detail', [
                'title' => $contentViewModel->editorTitle,
                'contentViewModel' => $contentViewModel,
                'formAction' => '/cms/events/dance-detail/' . $contentViewModel->detailSlug,
                'error' => $e->getMessage(),
                'success' => false,
            ]);
        }
    }

    private function mapHomePostToFormData(DanceHomeContentRequest $request): DanceHomeContentViewModel
    {
        $artistRows = [];
        $artists = $request->artists();
        foreach ($artists as $artist) {
            if (!$artist instanceof DanceHomeArtistRowRequest) {
                continue;
            }

            $artistRows[] = new DanceHomeArtistRowViewModel(
                $artist->id(),
                $artist->name(),
                $artist->genre(),
                $artist->image()
            );
        }

        $passRows = [];
        $passes = $request->passes();
        foreach ($passes as $pass) {
            if (!$pass instanceof DanceHomePassRowRequest) {
                continue;
            }

            $passRows[] = new DanceHomePassRowViewModel(
                $pass->id(),
                $pass->label(),
                $pass->price(),
                $pass->highlight()
            );
        }

        return new DanceHomeContentViewModel(
            $request->scheduleTitle(),
            $request->bannerBadge(),
            $request->bannerTitle(),
            $request->bannerDescription(),
            $request->artistsTitle(),
            $artistRows,
            $request->importantInformationTitle(),
            $request->importantInformationHtml(),
            $request->passesTitle(),
            $passRows,
            $request->capacityTitle(),
            $request->capacityHtml(),
            $request->specialTitle(),
            $request->specialHtml()
        );
    }

    private function mapDetailPostToFormData(string $detailSlug, DanceDetailContentRequest $request): DanceDetailContentViewModel
    {
        $baseViewModel = $this->danceService->getDanceDetailFormData($detailSlug);

        $heroImages = [];
        foreach ($request->heroImages() as $row) {
            if (!$row instanceof DanceDetailHeroImageRowRequest) {
                continue;
            }

            $heroImages[] = new DanceDetailHeroImageRowViewModel($row->id(), $row->image(), $row->alt());
        }

        $highlights = [];
        foreach ($request->highlights() as $row) {
            if (!$row instanceof DanceDetailHighlightRowRequest) {
                continue;
            }

            $highlights[] = new DanceDetailHighlightRowViewModel($row->id(), $row->icon(), $row->title(), $row->content());
        }

        $tracks = [];
        foreach ($request->tracks() as $row) {
            if (!$row instanceof DanceDetailTrackRowRequest) {
                continue;
            }

            $tracks[] = new DanceDetailTrackRowViewModel($row->id(), $row->title(), $row->subtitle(), $row->year(), $row->image());
        }

        return new DanceDetailContentViewModel(
            $baseViewModel->detailSlug,
            $baseViewModel->editorTitle,
            $baseViewModel->publicPath,
            $baseViewModel->performerName,
            $request->heroTitle(),
            $request->heroBadge(),
            $request->heroSubtitle(),
            $heroImages,
            $request->highlightsTitle(),
            $highlights,
            $request->tracksTitle(),
            $request->tracksNote(),
            $tracks,
            $request->importantInformationTitle(),
            $request->importantInformationHtml()
        );
    }
}
