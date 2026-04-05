<?php

namespace App\Service\Cms\Interfaces;

interface ICmsTicketManagementService
{
    public function getDashboardData(): array;
    public function getSoldTicketsData(?int $eventId, string $paymentStatus = 'all'): array;
    public function getSoldTicketQrData(int $ticketId): array;
    public function getOrdersData(?int $eventId, string $paymentStatus = 'all'): array;
    public function getOrderDetailData(int $orderId): array;
}
