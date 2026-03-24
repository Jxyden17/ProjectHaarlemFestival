<?php

namespace App\Mapper;

class PersonalProgramMapper
{
    public function map(array $rows): array
    {
        $grouped = [];

        foreach ($rows as $row) {
            $date = $row['date'];
            $sessionId = $row['session_id'];

            if (!isset($grouped[$date][$sessionId])) {
                $grouped[$date][$sessionId] = [
                    'event'   => $row['event_name'],
                    'time'    => $row['start_time'],
                    'venue'   => $row['venue_name'],
                    'price'   => $row['price'],
                    'qr'      => $row['qr_code'],
                    'tickets' => 0
                ];
            }

            $grouped[$date][$sessionId]['tickets']++;
        }

        return $grouped;
    }
}