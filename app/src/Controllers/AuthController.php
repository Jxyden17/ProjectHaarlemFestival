<?php

namespace App\Controllers;

use App\Service\Interfaces\IAuthService;

class AuthController extends BaseController
{
    private IAuthService $authService;

    public function __construct(IAuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLogin()
    {
        $this->render('auth/login');
    }

    public function showRegister()
    {
        $this->render('auth/register');
    }

    public function login()
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$this->isValidUsername($username) || !$this->isValidPassword($password)) {
            $this->render('auth/login', ['error' => 'Invalid username or password.']);
            return;
        }

        try {
            $user = $this->authService->login($username, $password);
            $_SESSION['user_id'] = $user->id;
            $_SESSION['username'] = $user->username;

            header('Location: /');
            exit;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->render('auth/login', ['error' => 'Login failed.']);
        }
    }

    public function register()
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$this->isValidUsername($username) || !$this->isValidPassword($password)) {
            $this->render('auth/register', ['error' => 'Invalid username or password.']);
            return;
        }

        try {
            $this->authService->register($username, $password);
            header('Location: /login');
            exit;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->render('auth/register', ['error' => 'Registration failed.']);
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: /');
        exit;
    }

    private function isValidUsername(string $username): bool
    {
        if ($username === '') {
            return false;
        }

        // Make sure the username is between 3 and 20 characters and only allows letters, numbers, and underscores.
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
            return false;
        }

        return true;
    }

    private function isValidPassword(string $password): bool
    {
        return strlen($password) >= 6;
    }
}
