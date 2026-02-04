<?php

namespace App\Repository;

use App\Models\UserModel;
use App\Models\Database;
use App\Repository\Interfaces\IUserRepository;
use PDO;

class UserRepository implements IUserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Find a user by their email.
    public function findByEmail(string $email): ?UserModel
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new UserModel(
            (int)$row['id'],
            $row['email'],
            $row['password']
        );
    }

    // Find a user by their ID.
    public function findById(int $id): ?UserModel
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new UserModel(
            (int)$row['id'],
            $row['email'],
            $row['password']
        );
    }

    // Create a new user.
    public function create(string $email, string $password): UserModel
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (email, password) VALUES (:email, :password)"
        );

        $stmt->execute([
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT)
        ]);

        $id = (int)$this->db->lastInsertId();

        return new UserModel($id, $email, '');
    }

    // Update the user's password.
    public function updatePassword(int $userId, string $newPassword): void
    {
        $stmt = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->execute([
            ':password' => password_hash($newPassword, PASSWORD_DEFAULT),
            ':id' => $userId
        ]);
    }
}
