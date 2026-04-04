<?php

namespace App\Repository\Interfaces;

interface ITicketRepository
{
    public function getTicketsByEventId(int $eventId): array;
    public function getById(int $id): ?array;
    public function findByOrderId(int $orderId): array;
    public function create(int $userId, int $orderId, int $sessionId, int $statusId = 1, string $qrCode = ''): int;
    public function createBulk(int $userId, int $orderId, int $sessionId, int $quantity): int;
    public function updateQrCode(int $id, string $qrCode): bool;
    public function update(int $id, int $statusId, ?string $qrCode): bool;
    public function delete(int $id): bool;
    public function beginTransaction(): void;
    public function commitTransaction(): void;
    public function rollBackTransaction(): void;
}
