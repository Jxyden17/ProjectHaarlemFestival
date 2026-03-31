<?php

namespace App\Controllers\Cms;

use App\Service\Cms\Interfaces\ICmsJazzService;
use App\Controllers\BaseController;
<<<<<<< Updated upstream
use App\Mapper\CmsJazzViewModelMapper;
use App\Models\Requests\UpdateJazzHomeRequest;
=======
>>>>>>> Stashed changes

class CmsJazzController extends BaseController
{
    private ICmsJazzService $jazzService;
<<<<<<< Updated upstream
    private CmsJazzViewModelMapper $cmsJazzViewModelMapper;
   

    public function __construct(ICmsJazzService $jazzService, CmsJazzViewModelMapper $cmsJazzViewModelMapper)
    {
        $this->jazzService = $jazzService;
        $this->cmsJazzViewModelMapper = $cmsJazzViewModelMapper;
=======
   

    public function __construct(ICmsJazzService $jazzService)
    {
        $this->jazzService = $jazzService;
       
>>>>>>> Stashed changes
    }

    public function index(): void
    {
        $this->requireAdmin();
<<<<<<< Updated upstream
        $contentViewModel =$this->cmsJazzViewModelMapper->mapHomePageToEditViewModel( $this->jazzService->getJazzHomePage());
=======
       $contentViewModel = $this->jazzService->getJazzHomePage();
>>>>>>> Stashed changes

        $this->renderCms('cms/events/jazz-home', [
            'title' => 'Jazz Home Content',
            'contentViewModel' => $contentViewModel,
            'success' => isset($_GET['saved']),
        ]);

    }
    public function updateHome(): void
    {
        $this->requireAdmin();
<<<<<<< Updated upstream
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
=======
        $request = UpdateDanceHomeRequest::fromArray($_POST);

        try {
            $this->danceService->saveDanceHomePage($request);
            header('Location: /cms/events/dance-home?saved=1');
            exit;
        } catch (\Throwable $e) {
            $contentViewModel = $this->cmsDanceViewModelMapper->mapHomeRequestToEditViewModel($request);
            $this->renderCms('cms/events/dance-home', [
                'title' => 'Dance Home Content',
>>>>>>> Stashed changes
                'contentViewModel' => $contentViewModel,
                'error' => $e->getMessage(),
                'success' => false,
            ]);
        }
    }
}