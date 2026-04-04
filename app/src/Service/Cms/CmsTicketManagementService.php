<?php

namespace App\Service\Cms;

use App\Models\Enums\Event;
use App\Repository\CmsTicketManagementRepository;
use App\Service\Cms\Interfaces\ICmsTicketManagementService;

class CmsTicketManagementService implements ICmsTicketManagementService
{
    public function __construct(private CmsTicketManagementRepository $ticketManagementRepository)
    {
    }

    public function getDashboardData(): array
    {
        $eventMetricsRows = $this->ticketManagementRepository->getEventTicketMetrics();
        $issuedCountsRows = $this->ticketManagementRepository->getIssuedTicketCountsByEvent();
        $paymentTotals = $this->ticketManagementRepository->getPaymentStatusTotals();

        $metricsByEventId = [];
        foreach ($eventMetricsRows as $row) {
            $eventId = (int) ($row['event_id'] ?? 0);
            if ($eventId <= 0) {
                continue;
            }

            $metricsByEventId[$eventId] = [
                'sessionCount' => (int) ($row['session_count'] ?? 0),
                'capacityTotal' => (int) ($row['capacity_total'] ?? 0),
                'availableTotal' => (int) ($row['available_total'] ?? 0),
                'soldTotal' => (int) ($row['sold_total'] ?? 0),
                'unlimitedSessions' => (int) ($row['unlimited_sessions'] ?? 0),
                'issuedTicketCount' => 0,
            ];
        }

        foreach ($issuedCountsRows as $row) {
            $eventId = (int) ($row['event_id'] ?? 0);
            if ($eventId <= 0) {
                continue;
            }

            if (!isset($metricsByEventId[$eventId])) {
                $metricsByEventId[$eventId] = [
                    'sessionCount' => 0,
                    'capacityTotal' => 0,
                    'availableTotal' => 0,
                    'soldTotal' => 0,
                    'unlimitedSessions' => 0,
                    'issuedTicketCount' => 0,
                ];
            }

            $metricsByEventId[$eventId]['issuedTicketCount'] = (int) ($row['issued_ticket_count'] ?? 0);
        }

        $eventCards = [];
        $summary = [
            'ticketedEventCount' => 0,
            'sessionCount' => 0,
            'capacityTotal' => 0,
            'availableTotal' => 0,
            'soldTotal' => 0,
            'issuedTicketCount' => 0,
            'pendingPayments' => (int) ($paymentTotals['pending_payments'] ?? 0),
            'paidPayments' => (int) ($paymentTotals['paid_payments'] ?? 0),
            'failedPayments' => (int) ($paymentTotals['failed_payments'] ?? 0),
        ];

        foreach (Event::cases() as $event) {
            if (!$this->supportsTickets($event)) {
                continue;
            }

            $summary['ticketedEventCount']++;
            $eventMetrics = $metricsByEventId[$event->value] ?? [
                'sessionCount' => 0,
                'capacityTotal' => 0,
                'availableTotal' => 0,
                'soldTotal' => 0,
                'unlimitedSessions' => 0,
                'issuedTicketCount' => 0,
            ];

            $summary['sessionCount'] += $eventMetrics['sessionCount'];
            $summary['capacityTotal'] += $eventMetrics['capacityTotal'];
            $summary['availableTotal'] += $eventMetrics['availableTotal'];
            $summary['soldTotal'] += $eventMetrics['soldTotal'];
            $summary['issuedTicketCount'] += $eventMetrics['issuedTicketCount'];

            $eventCards[] = [
                'id' => $event->value,
                'label' => $event->label(),
                'sessionCount' => $eventMetrics['sessionCount'],
                'capacityTotal' => $eventMetrics['capacityTotal'],
                'availableTotal' => $eventMetrics['availableTotal'],
                'soldTotal' => $eventMetrics['soldTotal'],
                'unlimitedSessions' => $eventMetrics['unlimitedSessions'],
                'issuedTicketCount' => $eventMetrics['issuedTicketCount'],
            ];
        }

        return [
            'summary' => $summary,
            'events' => $eventCards,
        ];
    }

    private function supportsTickets(Event $event): bool
    {
        return $event !== Event::Yummy;
    }
}
