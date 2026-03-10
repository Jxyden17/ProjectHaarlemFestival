<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Service\Interfaces\ICmsService;

class CmsUsersController extends BaseController
{
    private ICmsService $cmsService;

    public function __construct(ICmsService $cmsService)
    {
        $this->cmsService = $cmsService;
    }

    public function index(): void
    {
        $this->requireAdmin();
        $searchQuery = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'created_at';
        $order = $_GET['order'] ?? 'desc';

        if (!empty($searchQuery)) {
            $users = $this->cmsService->searchUsers($searchQuery);
        } else {
            $users = $this->cmsService->sortUsers($sort, $order);
        }

        $this->renderCms('cms/users/index', ['title' => 'User Management', 'users' => $users, 'searchQuery' => $searchQuery, 'sort' => $sort, 'order' => $order]);
    }

    public function showCreateForm(): void
    {
        $this->requireAdmin();
        $this->renderCms('cms/users/create', ['title' => 'Create User']);
    }

    public function addUser(): void
    {
        $this->requireAdmin();
        try {
            $this->cmsService->addUser($_POST['email'] ?? '', $_POST['password'] ?? '', $_POST['role_id'] ?? 3);
            header('Location: /cms/users');
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->renderCms('cms/users/create', ['title' => 'Create User', 'error' => 'Failed to create user.']);
        }
        exit;
    }
    public function showAdminEditForm(): void
    {
        $this->requireAdmin();
        $this->showEditForm('cms/users/edit');
    }
    public function showSelfEditForm(): void
    {
        if ( $_SESSION['user_id'] == $_GET['id'])
        {
            $this->showEditForm('cms/users/editSelf');
        }
        else
        {
            http_response_code(403);
            header('Location: /');
            exit;
        }

    }

    private function showEditForm($path): void
    {
      
        $user = $this->cmsService->getUserById((int)($_GET['id'] ?? 0));
        if ($user === null) {
            http_response_code(404);
            $this->render('shared/error', ['errorTitle' => 'User not found', 'errorMessage' => 'The requested user does not exist.']);
            return;
        }
        $this->renderCms($path, ['title' => 'Edit User', 'user' => $user]);
 
    }
    public function editUserAsAdmin(): void
    {
        $this->requireAdmin();
        $this->editUser();
    }
    public function editUserAsUser(): void
    {
       if ( $_SESSION['user_id'] == $_POST['id'])
        {
            $this->editUser();
        }
        else
        {
            http_response_code(403);
            header('Location: /');
            exit;
        }
    }
    private function editUser(): void
    {
        try {
            $this->cmsService->updateUser((int)($_POST['id'] ?? 0), (string)($_POST['email'] ?? ''), (string)($_POST['password'] ?? ''), (int)($_POST['role_id'] ?? 0));
            header('Location: /cms/users');
        } catch (\Exception $e) {
            error_log($e->getMessage());
            header('Location: /cms/users');
        }
        exit;
    }

    public function showDeleteConfirmation(): void
    {
        $this->requireAdmin();
        $user = $this->cmsService->getUserById((int)($_GET['id'] ?? 0));
        if ($user === null) {
            http_response_code(404);
            $this->render('shared/error', ['errorTitle' => 'User not found', 'errorMessage' => 'The requested user does not exist.']);
            return;
        }
        $this->renderCms('cms/users/delete', ['title' => 'Delete User', 'user' => $user]);
    }

    public function deleteUser(): void
    {
        $this->requireAdmin();
        try {
            $this->cmsService->deleteUser((int)($_POST['id'] ?? 0));
            header('Location: /cms/users');
        } catch (\Exception $e) {
            error_log($e->getMessage());
            header('Location: /cms/users');
        }
        exit;
    }
}
