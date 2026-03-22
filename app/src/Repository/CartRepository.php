<?php

namespace App\Repository;

use App\Models\Database;
use App\Repository\Interfaces\ICartRepository;
use PDO;

class CartRepository implements ICartRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findActiveCartByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT *
             FROM shopping_carts
             WHERE user_id = :user_id AND status = :status
             LIMIT 1'
        );

        $stmt->execute([
            ':user_id' => $userId,
            ':status' => 'active',
        ]);

        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        return $cart ?: null;
    }

    public function findActiveCartByGuestToken(string $guestToken): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT *
             FROM shopping_carts
             WHERE guest_token = :guest_token AND status = :status
             LIMIT 1'
        );

        $stmt->execute([
            ':guest_token' => $guestToken,
            ':status' => 'active',
        ]);

        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        return $cart ?: null;
    }

    public function createCart(?int $userId, ?string $guestToken): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO shopping_carts (user_id, guest_token, status)
             VALUES (:user_id, :guest_token, :status)'
        );

        $stmt->execute([
            ':user_id' => $userId,
            ':guest_token' => $guestToken,
            ':status' => 'active',
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findCartItemsByCartId(int $cartId): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                sci.id,
                sci.cart_id,
                sci.session_id,
                sci.quantity,
                sci.unit_price,
                s.date,
                s.start_time,
                s.label,
                s.price AS current_price,
                s.available_spots,
                s.amount_sold,
                e.name AS event_name,
                v.venue_name,
                GROUP_CONCAT(DISTINCT p.performer_name ORDER BY p.performer_name SEPARATOR \' B2B \') AS performer_names
            FROM shopping_cart_items sci
            INNER JOIN sessions s ON s.id = sci.session_id
            LEFT JOIN events e ON e.id = s.event_id
            LEFT JOIN venues v ON v.id = s.venue_id
            LEFT JOIN session_performers sp ON sp.session_id = s.id
            LEFT JOIN performers p ON p.id = sp.performer_id
            WHERE sci.cart_id = :cart_id
            GROUP BY
                sci.id,
                sci.cart_id,
                sci.session_id,
                sci.quantity,
                sci.unit_price,
                s.date,
                s.start_time,
                s.label,
                s.price,
                s.available_spots,
                s.amount_sold,
                e.name,
                v.venue_name
            ORDER BY s.date ASC, s.start_time ASC, sci.id ASC'
        );

        $stmt->execute([':cart_id' => $cartId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function findCartItemByCartIdAndSessionId(int $cartId, int $sessionId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT *
             FROM shopping_cart_items
             WHERE cart_id = :cart_id AND session_id = :session_id
             LIMIT 1'
        );

        $stmt->execute([
            ':cart_id' => $cartId,
            ':session_id' => $sessionId,
        ]);

        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        return $item ?: null;
    }

    public function addCartItem(int $cartId, int $sessionId, int $quantity, float $unitPrice): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO shopping_cart_items (cart_id, session_id, quantity, unit_price)
             VALUES (:cart_id, :session_id, :quantity, :unit_price)'
        );

        $stmt->execute([
            ':cart_id' => $cartId,
            ':session_id' => $sessionId,
            ':quantity' => $quantity,
            ':unit_price' => $unitPrice,
        ]);
    }

    public function updateCartItemQuantity(int $cartItemId, int $quantity): void
    {
        $stmt = $this->db->prepare(
            'UPDATE shopping_cart_items
             SET quantity = :quantity
             WHERE id = :id'
        );

        $stmt->execute([
            ':quantity' => $quantity,
            ':id' => $cartItemId,
        ]);
    }

    public function updateCartItem(int $cartItemId, int $quantity, float $unitPrice): void
    {
        $stmt = $this->db->prepare(
            'UPDATE shopping_cart_items
             SET quantity = :quantity,
                 unit_price = :unit_price
             WHERE id = :id'
        );

        $stmt->execute([
            ':quantity' => $quantity,
            ':unit_price' => $unitPrice,
            ':id' => $cartItemId,
        ]);
    }

    public function removeCartItem(int $cartItemId): void
    {
        $stmt = $this->db->prepare(
            'DELETE FROM shopping_cart_items
             WHERE id = :id'
        );

        $stmt->execute([':id' => $cartItemId]);
    }

    public function clearCart(int $cartId): void
    {
        $stmt = $this->db->prepare(
            'DELETE FROM shopping_cart_items
             WHERE cart_id = :cart_id'
        );

        $stmt->execute([':cart_id' => $cartId]);
    }

    public function findSessionById(int $sessionId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT
                s.id,
                s.event_id,
                s.venue_id,
                s.date,
                s.start_time,
                s.label,
                s.price,
                s.pricing_type,
                s.minimum_price,
                s.available_spots,
                s.amount_sold,
                e.name AS event_name,
                v.venue_name,
                GROUP_CONCAT(DISTINCT p.performer_name ORDER BY p.performer_name SEPARATOR \' B2B \') AS performer_names
            FROM sessions s
            LEFT JOIN events e ON e.id = s.event_id
            LEFT JOIN venues v ON v.id = s.venue_id
            LEFT JOIN session_performers sp ON sp.session_id = s.id
            LEFT JOIN performers p ON p.id = sp.performer_id
            WHERE s.id = :id
            GROUP BY
                s.id,
                s.event_id,
                s.venue_id,
                s.date,
                s.start_time,
                s.label,
                s.price,
                s.pricing_type,
                s.minimum_price,
                s.available_spots,
                s.amount_sold,
                e.name,
                v.venue_name
            LIMIT 1'
        );

        $stmt->execute([':id' => $sessionId]);

        $session = $stmt->fetch(PDO::FETCH_ASSOC);

        return $session ?: null;
    }


}
