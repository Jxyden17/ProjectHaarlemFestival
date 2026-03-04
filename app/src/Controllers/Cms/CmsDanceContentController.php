<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Models\Requests\Cms\Dance\DanceHomeArtistRowRequest;
use App\Models\Requests\Cms\Dance\DanceHomePassRowRequest;
use App\Models\ViewModels\Cms\Dance\DanceHomeArtistRowViewModel;
use App\Models\ViewModels\Cms\Dance\DanceHomeContentViewModel;
use App\Models\ViewModels\Cms\Dance\DanceHomePassRowViewModel;
use App\Models\Requests\Cms\DanceHomeContentRequest;
use App\Service\Interfaces\IDanceService;

class CmsDanceContentController extends BaseController
{
    private IDanceService $danceService;

    public function __construct(IDanceService $danceService)
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
            $contentViewModel = $this->mapPostToFormData($request);
            $this->renderCms('cms/events/dance-home', [
                'title' => 'Dance Home Content',
                'contentViewModel' => $contentViewModel,
                'error' => $e->getMessage(),
                'success' => false,
            ]);
        }
    }

    private function mapPostToFormData(DanceHomeContentRequest $request): DanceHomeContentViewModel
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
}
