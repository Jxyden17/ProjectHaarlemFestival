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

    public function getSoldTicketsData(?int $eventId, string $paymentStatus = 'all'): array
    {
        $ticketedEvents = array_values(array_filter(
            Event::cases(),
            fn(Event $event): bool => $this->supportsTickets($event)
        ));

        $selectedEvent = null;
        foreach ($ticketedEvents as $event) {
            if ($event->value === (int) $eventId) {
                $selectedEvent = $event;
                break;
            }
        }

        if ($selectedEvent === null) {
            $selectedEvent = $ticketedEvents[0] ?? Event::Dance;
        }

        $rows = $this->ticketManagementRepository->getSoldTicketsByEventId($selectedEvent->value);
        $normalizedPaymentStatus = strtolower(trim($paymentStatus));
        if (!in_array($normalizedPaymentStatus, ['all', 'paid', 'pending', 'failed'], true)) {
            $normalizedPaymentStatus = 'all';
        }

        $tickets = array_map(
            static fn(array $row): array => [
                'ticketId' => (int) ($row['ticket_id'] ?? 0),
                'orderId' => (int) ($row['order_id'] ?? 0),
                'customerEmail' => (string) ($row['customer_email'] ?? ''),
                'sessionId' => (int) ($row['session_id'] ?? 0),
                'sessionLabel' => trim((string) ($row['session_label'] ?? '')),
                'sessionDate' => (string) ($row['session_date'] ?? ''),
                'startTime' => (string) ($row['start_time'] ?? ''),
                'price' => (float) ($row['price'] ?? 0),
                'availableSpots' => (int) ($row['available_spots'] ?? 0),
                'amountSold' => (int) ($row['amount_sold'] ?? 0),
                'ticketStatus' => (string) ($row['ticket_status'] ?? 'Unknown'),
                'paymentStatus' => (string) ($row['payment_status'] ?? 'unknown'),
                'orderStatus' => (string) ($row['order_status'] ?? 'unknown'),
                'qrCode' => (string) ($row['qr_code'] ?? ''),
            ],
            $rows
        );

        if ($normalizedPaymentStatus !== 'all') {
            $tickets = array_values(array_filter(
                $tickets,
                static fn(array $ticket): bool => strtolower((string) ($ticket['paymentStatus'] ?? '')) === $normalizedPaymentStatus
            ));
        }

        return [
            'selectedEvent' => $selectedEvent,
            'eventTypes' => $ticketedEvents,
            'tickets' => $tickets,
            'paymentStatusFilter' => $normalizedPaymentStatus,
            'summary' => [
                'ticketCount' => count($tickets),
                'paidCount' => count(array_filter($tickets, static fn(array $ticket): bool => strtolower($ticket['paymentStatus']) === 'paid')),
                'pendingCount' => count(array_filter($tickets, static fn(array $ticket): bool => strtolower($ticket['paymentStatus']) === 'pending')),
            ],
        ];
    }

    private function supportsTickets(Event $event): bool
    {
        return $event !== Event::Yummy;
    }
}
