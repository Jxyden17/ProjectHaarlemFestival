<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Service\Interfaces\ICmsService;

class CmsTicketsController extends BaseController
{
    private ICmsService $cmsService;

    public function __construct(ICmsService $cmsService)
    {
        $this->cmsService = $cmsService;
    }

    public function index(): void
    {
        $this->requireAdmin();
        $this->renderCms('cms/tickets/index', ['title' => 'Ticket Management']);
    }
}
