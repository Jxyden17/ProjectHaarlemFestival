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
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$this->isValidEmail($email) || !$this->isValidPassword($password)) {
            $this->render('auth/login', ['error' => 'Invalid email or password.']);
            return;
        }

        try {
            $user = $this->authService->login($email, $password);
            $_SESSION['user_id'] = $user->id;
            $_SESSION['email'] = $user->email;

            header('Location: /');
            exit;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->render('auth/login', ['error' => 'Login failed.']);
        }
    }

    public function register()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$this->isValidEmail($email) || !$this->isValidPassword($password)) {
            $this->render('auth/register', ['error' => 'Invalid email or password.']);
            return;
        }

        try {
            $this->authService->register($email, $password);
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

    private function isValidEmail(string $email): bool
    {
        return $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function isValidPassword(string $password): bool
    {
        return strlen($password) >= 6;
    }
}
