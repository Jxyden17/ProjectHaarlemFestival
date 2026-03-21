<?php

namespace App\Repository\Interfaces;

interface ITicketRepository
{
    public function listByEventId(int $eventId): array;
    public function getById(int $id): ?array;
    public function createBulk(int $userId, int $orderId, int $sessionId, int $quantity): int;
    public function update(int $id, int $statusId, ?string $qrCode): bool;
    public function delete(int $id): bool;
}
