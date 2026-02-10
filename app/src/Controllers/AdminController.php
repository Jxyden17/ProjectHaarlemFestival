<?php

namespace App\Controllers;

use App\Service\Interfaces\IAdminService;

class AdminController extends BaseController
{
    private IAdminService $admin_service;

    public function __construct(IAdminService $admin_service)
    {
        $this->admin_service = $admin_service;
    }

    public function index(): void
    {
        $users = $this->admin_service->getAllUsers();

        $this->render('admin/users/index', ['title' => 'Admin Users','users' => $users]);
    }

    public function showCreateForm(): void
    {
        $this->render('admin/users/create', ['title' => 'Add New User']);
    }

    public function addUser(): void
    {
        try
        {
         $this->admin_service->addUser($_POST['email'] ?? '', $_POST['password'] ?? '', $_POST['role_id'] ?? 2);
         header('Location: /users');
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
            $this ->render('/users', ['error' => 'Failed to add user.']);
        }
        exit;
    }

    public function showEditForm(): void
    {
        $user = $this->admin_service->getUserById($_GET['id']);
        $this->render('admin/users/edit', ['title' => 'Edit User','user' => $user]);
    }
    public function editUser(): void
    {
        try 
        {
            $this->admin_service->updateUser($_POST['id'], $_POST['email'], $_POST['password'], $_POST['role_id']);
            header('Location: /users');
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this ->render('/users', ['error' => 'Failed to update user.']);
        }
    }

    public function showDeleteConfirmation(): void
    {
        $user = $this->admin_service->getUserById($_GET['id']);
        $this->render('admin/users/delete', [
            'title' => 'Delete User',
            'user' => $user
        ]);
    }


    public function deleteUser(): void
    { 
        try
        {
            $this->admin_service->deleteUser($_POST['id']);
            header('Location: /users');
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
            $this ->render('/users', ['error' => 'Failed to delete user.']);
        }
    }
}