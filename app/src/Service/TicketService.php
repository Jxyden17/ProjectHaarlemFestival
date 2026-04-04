<?php

namespace App\Service;

use App\Repository\Interfaces\ICartRepository;
use App\Repository\Interfaces\IPaymentRepository;
use App\Models\Enums\Event;
use App\Repository\Interfaces\ITicketRepository;
use App\Repository\Interfaces\IUserRepository;
use App\Service\Interfaces\IMailService;
use App\Service\Interfaces\ITicketService;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

class TicketService implements ITicketService
{
    private ITicketRepository $ticketRepo;
    private ICartRepository $cartRepository;
    private IPaymentRepository $paymentRepository;
    private IUserRepository $userRepository;
    private IMailService $mailService;

    public function __construct(
        ITicketRepository $ticketRepo,
        ICartRepository $cartRepository,
        IPaymentRepository $paymentRepository,
        IUserRepository $userRepository,
        IMailService $mailService
    )
    {
        $this->ticketRepo = $ticketRepo;
        $this->cartRepository = $cartRepository;
        $this->paymentRepository = $paymentRepository;
        $this->userRepository = $userRepository;
        $this->mailService = $mailService;
    }

    // Returns CMS ticket data per event while keeping all DB access in the repository layer.
    public function getTicketsByEvent(Event $event): array
    {
        return $this->ticketRepo->getTicketsByEventId($event->value);
    }

    // Orchestrates paid-order fulfillment so webhook logic stays thin and idempotent.
    public function fulfillPaidOrder(int $orderId, int $cartId): void
    {
        $this->assertValidPaidReferences($orderId, $cartId);
        $context = $this->loadFulfillmentContext($orderId, $cartId);

        $createdTickets = [];

        $this->ticketRepo->beginTransaction();

        try {
            $existingTickets = $this->ticketRepo->findByOrderId($orderId);
            if ($existingTickets !== []) {
                $this->markFulfillmentAsPaid($orderId, $cartId);
                $this->ticketRepo->commitTransaction();
                return;
            }

            $createdTickets = $this->createTicketsForCartItems($context['cartItems'], (int) $context['user_id'], $orderId);
            $this->markFulfillmentAsPaid($orderId, $cartId);
            $this->ticketRepo->commitTransaction();
        } catch (\Throwable $e) {
            try {
                $this->ticketRepo->rollBackTransaction();
            } catch (\Throwable) {
            }

            throw $e;
        }

        $this->sendTicketEmail((string) $context['user_email'], $orderId, $createdTickets);
    }

    // Validates the minimal paid references up front so later logic can assume real identifiers.
    private function assertValidPaidReferences(int $orderId, int $cartId): void
    {
        if ($orderId <= 0) {
            throw new \RuntimeException('Invalid paid order.');
        }

        if ($cartId <= 0) {
            throw new \RuntimeException('Invalid paid cart.');
        }
    }

    // Loads the order, user and cart items once so fulfillment logic works with a complete context.
    private function loadFulfillmentContext(int $orderId, int $cartId): array
    {
        $order = $this->paymentRepository->findOrderById($orderId);
        if ($order === null) {
            throw new \RuntimeException('Order not found for ticket fulfillment.');
        }

        $userId = (int) ($order['user_id'] ?? 0);
        if ($userId <= 0) {
            throw new \RuntimeException('Order user is missing.');
        }

        $user = $this->userRepository->findById($userId);
        if ($user === null) {
            throw new \RuntimeException('Ticket recipient not found.');
        }

        $cartItems = $this->cartRepository->findCartItemsByCartId($cartId);
        if ($cartItems === []) {
            throw new \RuntimeException('Paid cart has no items to fulfill.');
        }

        return [
            'order' => $order,
            'user_id' => $userId,
            'user_email' => $user->email,
            'cartItems' => $cartItems,
        ];
    }

    // Creates all ticket rows for the cart and updates sold counts in the same transaction.
    private function createTicketsForCartItems(array $cartItems, int $userId, int $orderId): array
    {
        $createdTickets = [];

        foreach ($cartItems as $cartItem) {
            $createdTickets = array_merge(
                $createdTickets,
                $this->createTicketsForCartItem($cartItem, $userId, $orderId)
            );
        }

        return $createdTickets;
    }

    // Expands a single cart line into concrete ticket rows so quantity becomes individual tickets.
    private function createTicketsForCartItem(array $cartItem, int $userId, int $orderId): array
    {
        $sessionId = (int) ($cartItem['session_id'] ?? 0);
        $quantity = (int) ($cartItem['quantity'] ?? 0);

        if ($sessionId <= 0 || $quantity <= 0) {
            throw new \RuntimeException('Paid cart contains an invalid ticket line.');
        }

        $createdTickets = [];

        for ($i = 0; $i < $quantity; $i++) {
            $createdTickets[] = $this->createSingleTicket($cartItem, $userId, $orderId, $sessionId);
        }

        $this->cartRepository->incrementSessionAmountSold($sessionId, $quantity);

        return $createdTickets;
    }

    // Creates one ticket record and returns the email-ready ticket data in one place.
    private function createSingleTicket(array $cartItem, int $userId, int $orderId, int $sessionId): array
    {
        $ticketId = $this->ticketRepo->create($userId, $orderId, $sessionId);
        $qrCode = $this->generateQrCodeString($ticketId);
        $this->ticketRepo->updateQrCode($ticketId, $qrCode);

        return [
            'ticket_id' => $ticketId,
            'order_id' => $orderId,
            'session_id' => $sessionId,
            'event_name' => (string) ($cartItem['event_name'] ?? 'Haarlem Festival'),
            'venue_name' => (string) ($cartItem['venue_name'] ?? 'Unknown venue'),
            'date' => (string) ($cartItem['date'] ?? ''),
            'start_time' => (string) ($cartItem['start_time'] ?? ''),
            'performer_names' => (string) ($cartItem['performer_names'] ?? ''),
            'qr_code' => $qrCode,
        ];
    }

    // Applies the paid state after successful fulfillment so all payment tables stay aligned.
    private function markFulfillmentAsPaid(int $orderId, int $cartId): void
    {
        $this->paymentRepository->updatePaymentStatusByOrderId($orderId, 'paid');
        $this->paymentRepository->markOrderAsPaid($orderId);
        $this->paymentRepository->markCartAsPaid($cartId);
    }

    // Generates a unique stored QR payload so each ticket can be referenced independently later.
    private function generateQrCodeString(int $ticketId): string
    {
        return 'HF-TICKET-' . $ticketId . '-' . strtoupper(bin2hex(random_bytes(8)));
    }

    // Renders the stored QR payload to PNG bytes so the same content can be mailed inline and attached.
    private function renderQrCodeImage(string $qrCode): string
    {
        $builder = new Builder(
            writer: new PngWriter(),
            data: $qrCode,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 280,
            margin: 12
        );

        $result = $builder->build();

        return $result->getString();
    }

    // Builds and sends the ticket email after commit so mail failures never roll back paid tickets.
    private function sendTicketEmail(string $email, int $orderId, array $tickets): void
    {
        if ($tickets === []) {
            return;
        }

        $mailPayload = $this->buildTicketMailPayload($orderId, $tickets);

        $sent = $this->mailService->sendTicketMail(
            $email,
            (string) $mailPayload['subject'],
            (string) $mailPayload['textBody'],
            (string) $mailPayload['htmlBody'],
            $mailPayload['inlineImages'],
            $mailPayload['attachments']
        );

        if (!$sent) {
            error_log('Ticket email failed for order #' . $orderId . '.');
        }
    }

    // Prepares the full email payload so TicketService keeps send logic and content assembly separate.
    private function buildTicketMailPayload(int $orderId, array $tickets): array
    {
        $plainLines = [
            'Hello,',
            '',
            'Your payment for order #' . $orderId . ' has been confirmed.',
            'Your tickets are listed below.',
            '',
        ];
        $htmlParts = [
            '<p>Hello,</p>',
            '<p>Your payment for order <strong>#' . $orderId . '</strong> has been confirmed.</p>',
            '<p>Your tickets are listed below.</p>',
        ];
        $inlineImages = [];
        $attachments = [];

        foreach ($tickets as $ticket) {
            $mailItem = $this->buildTicketMailItem($ticket);

            $plainLines = array_merge($plainLines, $mailItem['plainLines']);
            $htmlParts[] = (string) $mailItem['htmlBlock'];
            $inlineImages[] = $mailItem['inlineImage'];
            $attachments[] = $mailItem['attachment'];
        }

        return [
            'subject' => 'Your Haarlem Festival tickets',
            'textBody' => implode("\n", $plainLines),
            'htmlBody' => implode('', $htmlParts),
            'inlineImages' => $inlineImages,
            'attachments' => $attachments,
        ];
    }

    // Converts one created ticket into the plain/html/attachment fragments needed for the email.
    private function buildTicketMailItem(array $ticket): array
    {
        $ticketId = (int) ($ticket['ticket_id'] ?? 0);
        $cid = 'ticket-qr-' . $ticketId;
        $qrCode = (string) ($ticket['qr_code'] ?? '');
        $qrImage = $this->renderQrCodeImage($qrCode);
        $performersLabel = $this->resolvePerformerLabel($ticket);
        $eventName = (string) ($ticket['event_name'] ?? 'Haarlem Festival');
        $venueName = (string) ($ticket['venue_name'] ?? 'Unknown venue');
        $date = (string) ($ticket['date'] ?? '');
        $startTime = (string) ($ticket['start_time'] ?? '');

        return [
            'plainLines' => [
                'Ticket #' . $ticketId,
                'Event: ' . $eventName,
                'Line-up: ' . $performersLabel,
                'Venue: ' . $venueName,
                'Date: ' . $date,
                'Time: ' . $startTime,
                'QR code: ' . $qrCode,
                '',
            ],
            'htmlBlock' => $this->buildTicketHtmlBlock(
                $ticketId,
                $cid,
                $eventName,
                $performersLabel,
                $venueName,
                $date,
                $startTime,
                $qrCode
            ),
            'inlineImage' => [
                'cid' => $cid,
                'data' => $qrImage,
                'name' => 'ticket-' . $ticketId . '.png',
                'mimeType' => 'image/png',
            ],
            'attachment' => [
                'data' => $qrImage,
                'name' => 'ticket-' . $ticketId . '-qr.png',
                'mimeType' => 'image/png',
            ],
        ];
    }

    // Keeps the HTML fragment creation isolated so email markup changes stay local.
    private function buildTicketHtmlBlock(
        int $ticketId,
        string $cid,
        string $eventName,
        string $performersLabel,
        string $venueName,
        string $date,
        string $startTime,
        string $qrCode
    ): string
    {
        return
            '<hr>' .
            '<h3>Ticket #' . $ticketId . '</h3>' .
            '<p><strong>Event:</strong> ' . $this->escapeHtml($eventName) . '<br>' .
            '<strong>Line-up:</strong> ' . $this->escapeHtml($performersLabel) . '<br>' .
            '<strong>Venue:</strong> ' . $this->escapeHtml($venueName) . '<br>' .
            '<strong>Date:</strong> ' . $this->escapeHtml($date) . '<br>' .
            '<strong>Time:</strong> ' . $this->escapeHtml($startTime) . '<br>' .
            '<strong>QR code:</strong> ' . $this->escapeHtml($qrCode) . '</p>' .
            '<p><img src="cid:' . $cid . '" alt="QR code for ticket #' . $ticketId . '"></p>';
    }

    // Resolves a readable lineup label once so the mail builders do not duplicate fallback rules.
    private function resolvePerformerLabel(array $ticket): string
    {
        $performers = trim((string) ($ticket['performer_names'] ?? ''));

        if ($performers !== '') {
            return $performers;
        }

        return 'Festival session';
    }

    // Escapes mail content centrally so all HTML fragments are safe and formatted consistently.
    private function escapeHtml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
