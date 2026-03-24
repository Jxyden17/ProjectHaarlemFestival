<?php

namespace App\Repository;

use App\Repository\Interfaces\IPersonalProgramRepository;
use App\Models\Database;
use PDO;

class PersonalProgramRepository implements IPersonalProgramRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getUserTicketsWithSessions(int $userId): array
    {
        $sql = '
            SELECT 
                t.id as ticket_id,
                t.qr_code,
                s.id as session_id,
                s.date,
                s.start_time,
                s.price,
                COALESCE(e.name, s.label, "No event") as event_name,
                COALESCE(v.venue_name, "Unknown venue") as venue_name
            FROM tickets t
            INNER JOIN sessions s ON s.id = t.session_id
            LEFT JOIN events e ON e.id = s.event_id
            LEFT JOIN venues v ON v.id = s.venue_id
            LEFT JOIN session_performers sp ON sp.session_id = s.id
            WHERE t.user_id = :user_id
            GROUP BY 
                t.id,
                t.qr_code,
                s.id,
                s.date,
                s.start_time,
                s.price,
                e.name,
                v.venue_name
            ORDER BY s.date ASC, s.start_time ASC
        ';

        $stmt = $this->db->prepare($sql);

        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}