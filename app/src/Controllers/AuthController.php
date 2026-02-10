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
        $turnstileToken = $_POST['cf-turnstile-response'] ?? '';

        if (!$this->isValidEmail($email) || !$this->isValidPassword($password)) {
            $this->render('auth/register', ['error' => 'Invalid email or password.']);
            return;
        }

        if (!$this->verifyTurnstileToken($turnstileToken)) {
            $this->render('auth/register', ['error' => 'Captcha validation failed.']);
            return;
        }

        try {
            $this->authService->register($email, $password);
            header('Location: /login');
            exit;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->render('auth/register', ['error' => $e->getMessage()]);
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

    private function verifyTurnstileToken(string $token): bool
    {
        $secret = $_ENV['TURNSTILE_SECRET_KEY'] ?? getenv('TURNSTILE_SECRET_KEY');
        if (!$secret || $token === '') {
            return false;
        }

        $data = [
            'secret' => $secret,
            'response' => $token,
        ];

        $remoteip = $_SERVER['HTTP_CF_CONNECTING_IP']
            ?? $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR']
            ?? null;

        if ($remoteip) {
            $data['remoteip'] = $remoteip;
        }

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
                'timeout' => 5,
            ],
        ];

        $context = stream_context_create($options);
        $response = file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, $context);

        if ($response === false) {
            return false;
        }

        $payload = json_decode($response, true);

        if($payload['success'] == true) {
            return true;
        }
        return false;
    }
}
