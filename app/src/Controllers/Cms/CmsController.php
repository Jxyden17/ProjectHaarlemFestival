<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Service\Interfaces\ICmsService;

class CmsController extends BaseController
{
    private ICmsService $cmsService;

    public function __construct(ICmsService $cmsService)
    {
        $this->cmsService = $cmsService;
    }

    public function index(): void
    {
        $this->requireAdmin();
        $this->renderCms('cms/index', ['title' => 'CMS Dashboard']);
    }
}
