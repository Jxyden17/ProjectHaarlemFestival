<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Service\Cms\Interfaces\ICmsTicketManagementService;
use App\Service\Cms\TicketQrCodeService;

class CmsTicketManagementController extends BaseController
{
    public function __construct(
        private ICmsTicketManagementService $cmsTicketManagementService,
        private TicketQrCodeService $ticketQrCodeService
    )
    {
    }

    public function index(): void
    {
        $this->requireAdmin();

        $dashboardData = $this->cmsTicketManagementService->getDashboardData();

        $this->renderCms('cms/tickets/index', [
            'title' => 'Ticket Management',
            'summary' => $dashboardData['summary'] ?? [],
            'events' => $dashboardData['events'] ?? [],
        ]);
    }

    public function sold(): void
    {
        $this->requireAdmin();

        $eventId = isset($_GET['event_id']) ? (int) $_GET['event_id'] : null;
        $paymentStatus = trim((string) ($_GET['payment_status'] ?? 'all'));
        $soldTicketsData = $this->cmsTicketManagementService->getSoldTicketsData($eventId, $paymentStatus);

        $this->renderCms('cms/tickets/sold', [
            'title' => 'Sold Tickets',
            'selectedEvent' => $soldTicketsData['selectedEvent'] ?? null,
            'eventTypes' => $soldTicketsData['eventTypes'] ?? [],
            'tickets' => $soldTicketsData['tickets'] ?? [],
            'paymentStatusFilter' => $soldTicketsData['paymentStatusFilter'] ?? 'all',
            'summary' => $soldTicketsData['summary'] ?? [],
        ]);
    }

    public function orders(): void
    {
        $this->requireAdmin();

        $eventId = isset($_GET['event_id']) ? (int) $_GET['event_id'] : null;
        $paymentStatus = trim((string) ($_GET['payment_status'] ?? 'all'));
        $ordersData = $this->cmsTicketManagementService->getOrdersData($eventId, $paymentStatus);

        $this->renderCms('cms/tickets/orders', [
            'title' => 'Orders',
            'selectedEvent' => $ordersData['selectedEvent'] ?? null,
            'eventTypes' => $ordersData['eventTypes'] ?? [],
            'paymentStatusFilter' => $ordersData['paymentStatusFilter'] ?? 'all',
            'orders' => $ordersData['orders'] ?? [],
            'summary' => $ordersData['summary'] ?? [],
        ]);
    }

    public function orderDetail(): void
    {
        $this->requireAdmin();

        $orderId = (int) ($_GET['order_id'] ?? 0);
        $orderDetailData = $this->cmsTicketManagementService->getOrderDetailData($orderId);

        $this->renderCms('cms/tickets/order-detail', [
            'title' => 'Order Detail',
            'order' => $orderDetailData['order'] ?? [],
            'cartItems' => $orderDetailData['cartItems'] ?? [],
            'tickets' => $orderDetailData['tickets'] ?? [],
            'summary' => $orderDetailData['summary'] ?? [],
        ]);
    }

    public function qr(): void
    {
        $this->requireAdmin();

        $ticketId = (int) ($_GET['ticket_id'] ?? 0);

        try {
            $ticket = $this->cmsTicketManagementService->getSoldTicketQrData($ticketId);

            header('Content-Type: image/png');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            echo $this->ticketQrCodeService->renderPng((string) ($ticket['qrCode'] ?? ''));
        } catch (\Throwable $e) {
            http_response_code(404);
            header('Content-Type: text/plain; charset=UTF-8');
            echo 'QR unavailable';
        }

        exit;
    }
}
