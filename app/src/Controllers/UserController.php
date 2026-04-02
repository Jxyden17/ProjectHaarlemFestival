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
        $userId = (int) ($_GET['id'] ?? ($_SESSION['user_id'] ?? 0));
        $user = $this->cmsService->getUserById($userId);
        if ($user === null) {
            http_response_code(404);
            $this->render('shared/error', [
                'errorTitle' => 'User not found',
                'errorMessage' => 'The requested user does not exist.',
            ]);
            return;
        }

        $this->render("/User/index",['user' => $user]);
    }
}
?>
