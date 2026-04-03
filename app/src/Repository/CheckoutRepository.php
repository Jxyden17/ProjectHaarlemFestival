<?php

namespace App\Repository;

use App\Models\Database;
use App\Repository\Interfaces\ICheckoutRepository;
use PDO;

class CheckoutRepository implements ICheckoutRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function createOrder(int $userId, float $totalAmount): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO orders (user_id, total_amount)
             VALUES (:user_id, :total_amount)'
        );

        $stmt->execute([
            ':user_id' => $userId,
            ':total_amount' => $totalAmount,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function markCartAsConverted(int $cartId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE shopping_carts
             SET status = :status
             WHERE id = :id'
        );

        $stmt->execute([
            ':status' => 'paid',
            ':id' => $cartId,
        ]);
    }
}
