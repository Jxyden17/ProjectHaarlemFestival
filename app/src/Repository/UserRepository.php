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
        $stmt = $this->db->prepare("SELECT id, name, email,phone_number,country,city,addres,postcode, password, role_id, created_at FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return $this->makeUser($row);
    }

    private function makeUser($row)
    {
        return new UserModel(
    (int)($row['id'] ?? 0),
    $row['name'] ?? '',
    $row['email'] ?? '',
    $row['password'] ?? '',          
    $row['phone_number'] ?? '',      
    $row['country'] ?? '',
    $row['city'] ?? '',
    $row['addres'] ?? '',
    $row['postcode'] ?? '',
    (int)($row['role_id'] ?? 3),
    $row['created_at'] ?? ''
);
    }

    // Find a user by their ID.
    public function findById(int $id): ?UserModel
    {
        $stmt = $this->db->prepare("SELECT id, name, email,phone_number,country,city,addres,postcode, password, role_id, created_at FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

       return $this->makeUser($row);
    }

    // Create a new user.
    public function create(UserModel $user): UserModel
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email,phone_number,country,city,addres,postcode, password, role_id) VALUES (:name, :email, :phone_number, :country, :city, :addres, :postcode, :password, :role_id)"
        );

        $stmt->execute([
            ':name'=> $user->name,
            ':email' => $user->email,
            ':phone_number' => $user->phoneNumber,
            ':country' => $user->country,
            ':city' => $user->city,
            ':addres' => $user->addres,
            ':postcode' => $user->postcode,
            ':password' => password_hash($user->password, PASSWORD_DEFAULT),
            ':role_id' => $user->userRole->value
        ]);

        $id = (int)$this->db->lastInsertId();
        $createdAt = date('Y-m-d H:i:s');

        return new UserModel($id, $user->name, $user->email, " ", $user->phoneNumber, $user->country, $user->city, $user->addres, $user->postcode, $user->userRole->value, $createdAt);
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
        $stmt = $this->db->query("SELECT id, name, email,phone_number,country,city,addres,postcode, password, role_id, created_at  FROM users");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $users = [];
        foreach($rows as $row) {
            $users[] = $this->makeUser($row);
        }

        return $users;
    }

    public function deleteUser(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    public function updateUser(UserModel $user): UserModel
    {
        $params = [
            ':name'=> $user->name,
            ':email' => $user->email,
            ':phone_number' => $user->phoneNumber,
            ':country' => $user->country,
            ':city' => $user->city,
            ':addres' => $user->addres,
            ':postcode' => $user->postcode,
            ':role_id' => $user->userRole->value,
            ':id'=> $user->id
        ];

        if ($user->password !== '') {
            $stmt = $this->db->prepare(
                "UPDATE users SET name = :name, email = :email, phone_number = :phone_number, country = :country, city = :city, addres = :addres, postcode = :postcode, password = :password, role_id = :role_id WHERE id = :id"
            );
            $params[':password'] = password_hash($user->password, PASSWORD_DEFAULT);
        } else {
            $stmt = $this->db->prepare(
                "UPDATE users SET name = :name, email = :email, phone_number = :phone_number, country = :country, city = :city, addres = :addres, postcode = :postcode, role_id = :role_id WHERE id = :id"
            );
        }

        $stmt->execute($params);

        return $this->findById($user->id);
    }


    public function searchUsers(string $query): array
    {
        $stmt = $this->db->prepare("SELECT id, name, email,phone_number,country,city,addres,postcode, password, role_id, created_at  FROM users WHERE email LIKE :query");
        $stmt->execute([':query' => '%' . $query . '%']);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $users = [];
        foreach($rows as $row) {
            $users[] =$this->makeUser($row);
        }
        return $users;
    }

        public function sortUsers(string $sortBy, string $sortOrder): array
        {
            $allowedSortBy = ['email', 'role_id', 'created_at'];
            $allowedSortOrder = ['ASC', 'DESC'];
    
            if (!in_array($sortBy, $allowedSortBy)) {
                $sortBy = 'created_at'; 
            }
            if (!in_array(strtoupper($sortOrder), $allowedSortOrder)) {
                $sortOrder = 'DESC'; 
            }
    
            $stmt = $this->db->prepare("SELECT id, name, email,phone_number,country,city,addres,postcode, password, role_id, created_at FROM users ORDER BY $sortBy $sortOrder");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            $users = [];
            foreach($rows as $row) {
                $users[] = $this->makeUser($row);
            }
            return $users;
        }
}  
