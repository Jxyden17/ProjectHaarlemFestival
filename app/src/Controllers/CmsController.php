<?php

namespace App\Controllers;

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

    public function usersIndex(): void
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

    public function eventsIndex(): void
    {
        $this->requireAdmin();
        $this->renderCms('cms/events/index', ['title' => 'Event Management']);
    }

    public function ticketsIndex(): void
    {
        $this->requireAdmin();
        $this->renderCms('cms/tickets/index', ['title' => 'Ticket Management']);
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

    public function showEditForm(): void
    {
        $this->requireAdmin();
        $user = $this->cmsService->getUserById((int)($_GET['id'] ?? 0));
        if ($user === null) {
            http_response_code(404);
            $this->render('shared/error', ['errorTitle' => 'User not found', 'errorMessage' => 'The requested user does not exist.']);
            return;
        }
        $this->renderCms('cms/users/edit', ['title' => 'Edit User', 'user' => $user]);
    }

    public function editUser(): void
    {
        $this->requireAdmin();
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
