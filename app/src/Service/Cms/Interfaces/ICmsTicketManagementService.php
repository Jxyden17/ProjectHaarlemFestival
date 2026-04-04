<?php

namespace App\Service\Cms\Interfaces;

interface ICmsTicketManagementService
{
    public function getDashboardData(): array;
    public function getSoldTicketsData(?int $eventId, string $paymentStatus = 'all'): array;
}
