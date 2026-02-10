<?php

namespace App\Repository;

use App\Models\UserModel;
use App\Models\Enums\UserRole;
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
        $stmt = $this->db->prepare("SELECT id, email, password, role_id, created_at FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new UserModel(
            (int)$row['id'],
            $row['email'],
            $row['password'],
            (int)$row['role_id'],
            $row['created_at']
        );
    }

    // Find a user by their ID.
    public function findById(int $id): ?UserModel
    {
        $stmt = $this->db->prepare("SELECT id, email, password, role_id, created_at FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new UserModel(
            (int)$row['id'],
            $row['email'],
            $row['password'],
            (int)$row['role_id'],
            $row['created_at']
        );
    }

    // Create a new user.
    public function create(string $email, string $password): UserModel
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (email, password, role_id) VALUES (:email, :password, :role_id)"
        );
         
        $roleId = UserRole::Customer->value; // Standaard rol is Customer (ID 2)

        $stmt->execute([
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':role_id' => $roleId
        ]);

        $id = (int)$this->db->lastInsertId();
        $createdAt = date('Y-m-d H:i:s');

        return new UserModel($id, $email, '', $roleId, $createdAt);
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

    public function getAllUsers(): array
    {
        $stmt = $this->db->query("SELECT id, email, password, role_id, created_at FROM users");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $users = [];
        foreach($rows as $row) {
            $users[] = new UserModel(
                (int)$row['id'],
                $row['email'],
                $row['password'],
                (int)$row['role_id'],
                $row['created_at']
            );
        }
        return $users;
    }

    public function addUsers(string $email, string $password, int $roleId): UserModel
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (email, password, role_id) VALUES (:email, :password, :role_id)"
        );

        $stmt->execute([
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':role_id' => $roleId
        ]);

        $id = (int)$this->db->lastInsertId();
        $createdAt = date('Y-m-d H:i:s');

        return new UserModel($id, $email, '', $roleId, $createdAt);
    }

     public function deleteUser(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    public function updateUser(int $id, string $email, string $password, int $roleId): UserModel
    {
        $stmt = $this->db->prepare(
            "UPDATE users SET email = :email, password = :password, role_id = :role_id WHERE id = :id"
        );

        $stmt->execute([
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':role_id' => $roleId,
            ':id' => $id
        ]);

        return $this->findById($id);
    }
}  