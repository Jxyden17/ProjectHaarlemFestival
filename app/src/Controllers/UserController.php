<?php

namespace App\Controllers;
use App\Controllers\CmsUsersController;
use App\Service\Cms\Interfaces\ICmsService;

class UserController extends BaseController
{
    private ICmsService $cmsService;

    public function __construct(ICmsService $cmsService)
    {  
        $this->cmsService = $cmsService;
    }
   public function index()
    {
        $this->requireAuth();
        $user = $this->cmsService->getUserById((int)($_GET['id'] ?? 0));
        $this->render("/User/index",['user' => $user]);
    }
}
?>