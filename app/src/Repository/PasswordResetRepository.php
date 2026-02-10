<?php

namespace App\Repository;

use App\Models\Database;
use App\Repository\Interfaces\IPasswordResetRepository;
use PDO;

class PasswordResetRepository implements IPasswordResetRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function deleteByUserId(int $userId): void
    {
        $stmt = $this->db->prepare('DELETE FROM password_resets WHERE user_id = :user_id');
        $stmt->execute([':user_id' => $userId]);
    }

    public function createPasswordReset(int $userId, string $tokenHash, string $expiresAt): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (:user_id, :token_hash, :expires_at)'
        );

        $stmt->execute([
            ':user_id' => $userId,
            ':token_hash' => $tokenHash,
            ':expires_at' => $expiresAt,
        ]);
    }

    public function findValidByTokenHash(string $tokenHash): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, user_id FROM password_resets WHERE token_hash = :token_hash AND expires_at > UTC_TIMESTAMP() LIMIT 1'
        );

        $stmt->execute([':token_hash' => $tokenHash]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function deleteById(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM password_resets WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}
