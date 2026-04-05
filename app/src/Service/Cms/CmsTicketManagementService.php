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
        $ticketedEvents = $this->getTicketedEvents();
        $selectedEvent = $this->resolveSelectedEvent($ticketedEvents, $eventId, true);

        $rows = $this->ticketManagementRepository->getSoldTicketsByEventId($selectedEvent->value);
        $normalizedPaymentStatus = $this->normalizePaymentStatus($paymentStatus);

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
                'failedCount' => count(array_filter($tickets, static fn(array $ticket): bool => strtolower($ticket['paymentStatus']) === 'failed')),
            ],
        ];
    }

    public function getSoldTicketQrData(int $ticketId): array
    {
        if ($ticketId <= 0) {
            throw new \RuntimeException('Missing ticket identifier.');
        }

        $row = $this->ticketManagementRepository->getSoldTicketById($ticketId);
        if ($row === null) {
            throw new \RuntimeException('Ticket not found.');
        }

        $qrCode = trim((string) ($row['qr_code'] ?? ''));
        if ($qrCode === '') {
            throw new \RuntimeException('This ticket does not have a QR code yet.');
        }

        return [
            'ticketId' => (int) ($row['ticket_id'] ?? 0),
            'orderId' => (int) ($row['order_id'] ?? 0),
            'eventId' => (int) ($row['event_id'] ?? 0),
            'customerEmail' => (string) ($row['customer_email'] ?? ''),
            'sessionLabel' => trim((string) ($row['session_label'] ?? '')),
            'sessionDate' => (string) ($row['session_date'] ?? ''),
            'startTime' => (string) ($row['start_time'] ?? ''),
            'ticketStatus' => (string) ($row['ticket_status'] ?? 'Unknown'),
            'paymentStatus' => (string) ($row['payment_status'] ?? 'unknown'),
            'orderStatus' => (string) ($row['order_status'] ?? 'unknown'),
            'qrCode' => $qrCode,
        ];
    }

    public function getOrdersData(?int $eventId, string $paymentStatus = 'all'): array
    {
        $ticketedEvents = $this->getTicketedEvents();
        $selectedEvent = $this->resolveSelectedEvent($ticketedEvents, $eventId, false);
        $normalizedPaymentStatus = $this->normalizePaymentStatus($paymentStatus);

        $rows = $this->ticketManagementRepository->getOrders($selectedEvent?->value);

        $orders = array_map(
            static fn(array $row): array => [
                'orderId' => (int) ($row['order_id'] ?? 0),
                'customerEmail' => (string) ($row['customer_email'] ?? ''),
                'totalAmount' => (float) ($row['total_amount'] ?? 0),
                'orderStatus' => (string) ($row['order_status'] ?? 'unknown'),
                'paymentStatus' => strtolower((string) ($row['payment_status'] ?? 'unknown')),
                'paymentMethod' => (string) ($row['payment_method'] ?? ''),
                'cartId' => (int) ($row['cart_id'] ?? 0),
                'expectedTicketCount' => (int) ($row['expected_ticket_count'] ?? 0),
                'issuedTicketCount' => (int) ($row['issued_ticket_count'] ?? 0),
                'sessionCount' => (int) ($row['session_count'] ?? 0),
                'eventNames' => (string) ($row['event_names'] ?? ''),
                'createdAt' => (string) ($row['created_at'] ?? ''),
            ],
            $rows
        );

        if ($normalizedPaymentStatus !== 'all') {
            $orders = array_values(array_filter(
                $orders,
                static fn(array $order): bool => ($order['paymentStatus'] ?? 'unknown') === $normalizedPaymentStatus
            ));
        }

        return [
            'selectedEvent' => $selectedEvent,
            'eventTypes' => $ticketedEvents,
            'paymentStatusFilter' => $normalizedPaymentStatus,
            'orders' => $orders,
            'summary' => [
                'orderCount' => count($orders),
                'paidCount' => count(array_filter($orders, static fn(array $order): bool => $order['paymentStatus'] === 'paid')),
                'pendingCount' => count(array_filter($orders, static fn(array $order): bool => $order['paymentStatus'] === 'pending')),
                'failedCount' => count(array_filter($orders, static fn(array $order): bool => $order['paymentStatus'] === 'failed')),
                'grossAmount' => array_reduce($orders, static fn(float $sum, array $order): float => $sum + (float) ($order['totalAmount'] ?? 0), 0.0),
            ],
        ];
    }

    public function getOrderDetailData(int $orderId): array
    {
        if ($orderId <= 0) {
            throw new \RuntimeException('Missing order identifier.');
        }

        $row = $this->ticketManagementRepository->getOrderById($orderId);
        if ($row === null) {
            throw new \RuntimeException('Order not found.');
        }

        $cartId = (int) ($row['cart_id'] ?? 0);
        $cartItemsRows = $cartId > 0 ? $this->ticketManagementRepository->getOrderCartItems($cartId) : [];
        $ticketsRows = $this->ticketManagementRepository->getOrderTickets($orderId);

        $order = [
            'orderId' => (int) ($row['order_id'] ?? 0),
            'customerEmail' => (string) ($row['customer_email'] ?? ''),
            'totalAmount' => (float) ($row['total_amount'] ?? 0),
            'orderStatus' => (string) ($row['order_status'] ?? 'unknown'),
            'paymentStatus' => strtolower((string) ($row['payment_status'] ?? 'unknown')),
            'paymentMethod' => (string) ($row['payment_method'] ?? ''),
            'providerPaymentId' => (string) ($row['provider_payment_id'] ?? ''),
            'cartId' => $cartId,
            'expectedTicketCount' => (int) ($row['expected_ticket_count'] ?? 0),
            'issuedTicketCount' => (int) ($row['issued_ticket_count'] ?? 0),
            'sessionCount' => (int) ($row['session_count'] ?? 0),
            'eventNames' => (string) ($row['event_names'] ?? ''),
            'createdAt' => (string) ($row['created_at'] ?? ''),
        ];

        $cartItems = array_map(
            static fn(array $item): array => [
                'cartItemId' => (int) ($item['cart_item_id'] ?? 0),
                'sessionId' => (int) ($item['session_id'] ?? 0),
                'quantity' => (int) ($item['quantity'] ?? 0),
                'unitPrice' => (float) ($item['unit_price'] ?? 0),
                'eventName' => (string) ($item['event_name'] ?? ''),
                'sessionLabel' => trim((string) ($item['session_label'] ?? '')),
                'sessionDate' => (string) ($item['session_date'] ?? ''),
                'startTime' => (string) ($item['start_time'] ?? ''),
                'venueName' => (string) ($item['venue_name'] ?? ''),
            ],
            $cartItemsRows
        );

        $tickets = array_map(
            static fn(array $ticket): array => [
                'ticketId' => (int) ($ticket['ticket_id'] ?? 0),
                'customerEmail' => (string) ($ticket['customer_email'] ?? ''),
                'sessionId' => (int) ($ticket['session_id'] ?? 0),
                'eventName' => (string) ($ticket['event_name'] ?? ''),
                'sessionLabel' => trim((string) ($ticket['session_label'] ?? '')),
                'sessionDate' => (string) ($ticket['session_date'] ?? ''),
                'startTime' => (string) ($ticket['start_time'] ?? ''),
                'venueName' => (string) ($ticket['venue_name'] ?? ''),
                'ticketStatus' => (string) ($ticket['ticket_status'] ?? 'Unknown'),
                'paymentStatus' => strtolower((string) ($ticket['payment_status'] ?? 'unknown')),
                'qrCode' => (string) ($ticket['qr_code'] ?? ''),
            ],
            $ticketsRows
        );

        return [
            'order' => $order,
            'cartItems' => $cartItems,
            'tickets' => $tickets,
            'summary' => [
                'cartLineCount' => count($cartItems),
                'expectedTicketCount' => (int) ($order['expectedTicketCount'] ?? 0),
                'issuedTicketCount' => count($tickets),
                'qrReadyCount' => count(array_filter($tickets, static fn(array $ticket): bool => ((string) ($ticket['qrCode'] ?? '')) !== '')),
            ],
        ];
    }

    private function supportsTickets(Event $event): bool
    {
        return $event !== Event::Yummy;
    }

    private function getTicketedEvents(): array
    {
        return array_values(array_filter(
            Event::cases(),
            fn(Event $event): bool => $this->supportsTickets($event)
        ));
    }

    private function resolveSelectedEvent(array $ticketedEvents, ?int $eventId, bool $fallbackToFirst): ?Event
    {
        foreach ($ticketedEvents as $event) {
            if ($event->value === (int) $eventId) {
                return $event;
            }
        }

        return $fallbackToFirst ? ($ticketedEvents[0] ?? Event::Dance) : null;
    }

    private function normalizePaymentStatus(string $paymentStatus): string
    {
        $normalizedPaymentStatus = strtolower(trim($paymentStatus));

        if (!in_array($normalizedPaymentStatus, ['all', 'paid', 'pending', 'failed'], true)) {
            return 'all';
        }

        return $normalizedPaymentStatus;
    }
}
