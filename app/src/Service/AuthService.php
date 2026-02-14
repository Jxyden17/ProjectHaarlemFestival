<?php

namespace App\Service;

use App\Models\UserModel;
use App\Repository\Interfaces\IPasswordResetRepository;
use App\Repository\Interfaces\IUserRepository;
use App\Service\Interfaces\IAuthService;
use App\Service\Interfaces\IMailService;

class AuthService implements IAuthService
{
    private IUserRepository $userRepo;
    private IPasswordResetRepository $passwordResetRepo;
    private IMailService $mailService;

    public function __construct(
        IUserRepository $userRepo,
        IPasswordResetRepository $passwordResetRepo,
        IMailService $mailService
    ) {
        $this->userRepo = $userRepo;
        $this->passwordResetRepo = $passwordResetRepo;
        $this->mailService = $mailService;
    }

    public function login(string $email, string $password): UserModel
    {
        $user = $this->userRepo->findByEmail($email);

        if (!$user) {
            throw new \Exception('User not found');
        }

        if (!password_verify($password, $user->password)) {
            throw new \Exception('Invalid password');
        }

        return $user;
    }

    public function register(string $email, string $password): UserModel
    {
        $existing = $this->userRepo->findByEmail($email);
        if ($existing) {
            throw new \Exception('Email already taken');
        }

        return $this->userRepo->create($email, $password);
    }

    public function requestPasswordReset(string $email, string $baseUrl): void
    {
        $user = $this->userRepo->findByEmail($email);
        if (!$user) {
            return;
        }

        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = gmdate('Y-m-d H:i:s', time() + 3600);

        $this->passwordResetRepo->deleteByUserId($user->id);
        $this->passwordResetRepo->createPasswordReset($user->id, $tokenHash, $expiresAt);

        $resetLink = rtrim($baseUrl, '/') . '/reset-password?token=' . urlencode($token);

        $subject = 'Reset your Haarlem Festival password';
        $body = "Hello,\n\n"
            . "We received a request to reset your password.\n"
            . "Use this link to set a new password:\n"
            . $resetLink . "\n\n"
            . "This link expires in 1 hour.\n"
            . "If you did not request this, you can ignore this email.\n";

        if (!$this->mailService->sendMail($user->email, $subject, $body)) {
            throw new \Exception('Failed to send reset email');
        }
    }

    public function resetPassword(string $token, string $newPassword): void
    {
        $tokenHash = hash('sha256', $token);
        $resetRecord = $this->passwordResetRepo->findValidByTokenHash($tokenHash);

        if (!$resetRecord) {
            throw new \Exception('Invalid or expired reset token');
        }

        $this->userRepo->updatePassword((int)$resetRecord['user_id'], $newPassword);
        $this->passwordResetRepo->deleteById((int)$resetRecord['id']);
    }
}
