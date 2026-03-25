<?php

namespace App\Controllers\Cms;

use App\Service\Cms\Interfaces\ICmsJazzService;
use App\Controllers\BaseController;

class CmsJazzController extends BaseController
{
    private ICmsJazzService $jazzService;
   

    public function __construct(ICmsJazzService $jazzService)
    {
        $this->jazzService = $jazzService;
       
    }

    public function index(): void
    {
        $this->requireAdmin();
       $contentViewModel = $this->jazzService->getJazzHomePage();

        $this->renderCms('cms/events/jazz-home', [
            'title' => 'Jazz Home Content',
            'contentViewModel' => $contentViewModel,
            'success' => isset($_GET['saved']),
        ]);

    }
    public function updateHome(): void
    {
        $this->requireAdmin();
        $request = UpdateDanceHomeRequest::fromArray($_POST);

        try {
            $this->danceService->saveDanceHomePage($request);
            header('Location: /cms/events/dance-home?saved=1');
            exit;
        } catch (\Throwable $e) {
            $contentViewModel = $this->cmsDanceViewModelMapper->mapHomeRequestToEditViewModel($request);
            $this->renderCms('cms/events/dance-home', [
                'title' => 'Dance Home Content',
                'contentViewModel' => $contentViewModel,
                'error' => $e->getMessage(),
                'success' => false,
            ]);
        }
    }
}