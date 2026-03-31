<?php

namespace App\Controllers\Cms;

use App\Service\Cms\Interfaces\ICmsJazzService;
use App\Controllers\BaseController;
use App\Mapper\CmsJazzViewModelMapper;
use App\Models\Requests\UpdateJazzHomeRequest;

class CmsJazzController extends BaseController
{
    private ICmsJazzService $jazzService;
    private CmsJazzViewModelMapper $cmsJazzViewModelMapper;
   

    public function __construct(ICmsJazzService $jazzService, CmsJazzViewModelMapper $cmsJazzViewModelMapper)
    {
        $this->jazzService = $jazzService;
        $this->cmsJazzViewModelMapper = $cmsJazzViewModelMapper;
    }

    public function index(): void
    {
        $this->requireAdmin();
        $contentViewModel =$this->cmsJazzViewModelMapper->mapHomePageToEditViewModel( $this->jazzService->getJazzHomePage());

        $this->renderCms('cms/events/jazz-home', [
            'title' => 'Jazz Home Content',
            'contentViewModel' => $contentViewModel,
            'success' => isset($_GET['saved']),
        ]);

    }
    public function updateHome(): void
    {
        $this->requireAdmin();
        $request = UpdateJazzHomeRequest::fromArray($_POST);

        try {
            $this->jazzService->saveJazzHomePage($request);
            header('Location: /cms/events/jazz-home?saved=1');
            exit;
        } catch (\Throwable $e) {
           
            var_dump($e->getMessage());
            die();
            $contentViewModel =$this->cmsJazzViewModelMapper->mapHomeRequestToEditViewModel($request);
            $this->renderCms('cms/events/jazz-home', [
                'title' => 'jazz Home Content',
                'contentViewModel' => $contentViewModel,
                'error' => $e->getMessage(),
                'success' => false,
            ]);
        }
    }
}