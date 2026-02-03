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

    // Find a user by their username.
    public function findByUsername(string $username): ?UserModel
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new UserModel(
            (int)$row['id'],
            $row['username'],
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
            $row['username'],
            $row['password']
        );
    }

    // Create a new user.
    public function create(string $username, string $password): UserModel
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, password) VALUES (:username, :password)"
        );

        $stmt->execute([
            ':username' => $username,
            ':password' => password_hash($password, PASSWORD_DEFAULT)
        ]);

        $id = (int)$this->db->lastInsertId();

        return new UserModel($id, $username, '');
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
