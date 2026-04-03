<?php

namespace App\Repository;

use App\Models\Database;
use App\Repository\Interfaces\IFavoritesRepository;
use PDO;

class FavoritesRepository implements IFavoritesRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findFavoritesByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                f.id,
                f.user_id,
                f.session_id,
                f.created_at,
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
             FROM favorites f
             INNER JOIN sessions s ON s.id = f.session_id
             LEFT JOIN events e ON e.id = s.event_id
             LEFT JOIN venues v ON v.id = s.venue_id
             LEFT JOIN session_performers sp ON sp.session_id = s.id
             LEFT JOIN performers p ON p.id = sp.performer_id
             WHERE f.user_id = :user_id
             GROUP BY
                f.id,
                f.user_id,
                f.session_id,
                f.created_at,
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
             ORDER BY f.created_at DESC, f.id DESC'
        );

        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isFavorite(int $userId, int $sessionId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1
             FROM favorites
             WHERE user_id = :user_id AND session_id = :session_id
             LIMIT 1'
        );

        $stmt->execute([
            ':user_id' => $userId,
            ':session_id' => $sessionId,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function addFavorite(int $userId, int $sessionId): void
    {
        if ($this->isFavorite($userId, $sessionId)) {
            return;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO favorites (user_id, session_id)
             VALUES (:user_id, :session_id)'
        );

        $stmt->execute([
            ':user_id' => $userId,
            ':session_id' => $sessionId,
        ]);
    }

    public function removeFavorite(int $userId, int $sessionId): void
    {
        $stmt = $this->db->prepare(
            'DELETE FROM favorites
             WHERE user_id = :user_id AND session_id = :session_id'
        );

        $stmt->execute([
            ':user_id' => $userId,
            ':session_id' => $sessionId,
        ]);
    }
}
