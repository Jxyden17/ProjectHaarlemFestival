<?php

$order = is_array($order ?? null) ? $order : [];
$cartItems = is_array($cartItems ?? null) ? $cartItems : [];
$tickets = is_array($tickets ?? null) ? $tickets : [];
$summary = is_array($summary ?? null) ? $summary : [];

$orderId = (int) ($order['orderId'] ?? 0);
$cartId = (int) ($order['cartId'] ?? 0);
$expectedTicketCount = (int) ($summary['expectedTicketCount'] ?? 0);
$issuedTicketCount = (int) ($summary['issuedTicketCount'] ?? 0);
$qrReadyCount = (int) ($summary['qrReadyCount'] ?? 0);
$cartLineCount = (int) ($summary['cartLineCount'] ?? 0);
?>

<div class="container-lg py-4 py-md-5">
    <div class="vstack gap-3">
        <section class="cms-page-hero">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <p class="cms-page-hero__eyebrow">Ticket Management</p>
                    <h1 class="cms-page-hero__title">Order #<?= $orderId ?></h1>
                    <p class="cms-page-hero__description">Purchase overview.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/cms/tickets/orders" class="btn btn-outline-secondary">Back to Orders</a>
                    <a href="/cms/tickets" class="btn btn-outline-secondary">Back to Ticket Hub</a>
                </div>
            </div>
        </section>

        <section class="card border-0 shadow-sm cms-ticket-focus-strip">
            <div class="card-body p-3 p-md-4">
                <div class="cms-ticket-overview-grid">
                    <div class="cms-ticket-overview-main">
                        <span class="cms-ticket-overview-main__eyebrow">Summary</span>
                        <div class="cms-ticket-overview-main__stats">
                            <div class="cms-ticket-overview-main__stat">
                                <span class="cms-ticket-overview-main__label">Total</span>
                                <strong class="cms-ticket-overview-main__value">EUR <?= number_format((float) ($order['totalAmount'] ?? 0), 2) ?></strong>
                            </div>
                            <div class="cms-ticket-overview-main__stat">
                                <span class="cms-ticket-overview-main__label">Ordered</span>
                                <strong class="cms-ticket-overview-main__value"><?= $expectedTicketCount ?></strong>
                            </div>
                            <div class="cms-ticket-overview-main__stat">
                                <span class="cms-ticket-overview-main__label">Created</span>
                                <strong class="cms-ticket-overview-main__value"><?= $issuedTicketCount ?></strong>
                            </div>
                            <div class="cms-ticket-overview-main__stat">
                                <span class="cms-ticket-overview-main__label">QR Ready</span>
                                <strong class="cms-ticket-overview-main__value"><?= $qrReadyCount ?></strong>
                            </div>
                        </div>
                        <div class="cms-ticket-overview-action">
                            <div>
                                <span class="cms-ticket-overview-action__label">Customer</span>
                                <p class="cms-ticket-overview-action__text mb-0"><?= htmlspecialchars((string) ($order['customerEmail'] ?? 'Unknown')) ?></p>
                            </div>
                            <div class="text-md-end">
                                <div class="small text-uppercase text-muted mb-1">Created</div>
                                <div><?= htmlspecialchars((string) ($order['createdAt'] ?? '')) ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="cms-ticket-overview-side">
                        <div class="cms-ticket-overview-side__alert">
                            <span class="cms-ticket-overview-side__label">Payment</span>
                            <strong class="cms-ticket-overview-side__value"><?= htmlspecialchars(ucfirst((string) ($order['paymentStatus'] ?? 'unknown'))) ?></strong>
                            <span class="cms-ticket-overview-side__meta"><?= htmlspecialchars((string) ($order['paymentMethod'] ?? '')) ?></span>
                        </div>
                        <div class="cms-ticket-overview-side__meta-grid">
                            <div class="cms-ticket-overview-side__meta-card">
                                <span class="cms-ticket-overview-side__meta-label">Status</span>
                                <strong class="cms-ticket-overview-side__meta-value"><?= htmlspecialchars(ucfirst((string) ($order['orderStatus'] ?? 'unknown'))) ?></strong>
                            </div>
                            <div class="cms-ticket-overview-side__meta-card">
                                <span class="cms-ticket-overview-side__meta-label">Reference</span>
                                <strong class="cms-ticket-overview-side__meta-value">#<?= $cartId ?></strong>
                            </div>
                        </div>
                        <div class="cms-ticket-status-inline">
                            <span class="cms-ticket-status-inline__label">Included</span>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2"><?= $cartLineCount ?> lines</span>
                                <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2"><?= (int) ($order['sessionCount'] ?? 0) ?> sessions</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="h5 mb-3">Order Info</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="small text-uppercase text-muted mb-1">Events</div>
                        <div><?= htmlspecialchars((string) ($order['eventNames'] ?? 'No event summary')) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-uppercase text-muted mb-1">Payment Reference</div>
                        <div class="text-break"><?= htmlspecialchars((string) ($order['providerPaymentId'] ?? '')) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="p-4 border-bottom border-secondary-subtle">
                    <h2 class="h5 mb-1">Order Items</h2>
                    <p class="text-muted mb-0">What the customer selected.</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Session</th>
                                <th>Venue</th>
                                <th>Date</th>
                                <th>Qty</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($cartItems === []): ?>
                                <tr>
                                    <td colspan="6" class="cms-empty-state">No cart items found for this order.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($cartItems as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars((string) ($item['eventName'] ?? 'Unknown')) ?></td>
                                        <td><?= htmlspecialchars((string) (($item['sessionLabel'] ?? '') !== '' ? $item['sessionLabel'] : ('Session #' . (int) ($item['sessionId'] ?? 0)))) ?></td>
                                        <td><?= htmlspecialchars((string) ($item['venueName'] ?? 'Unknown venue')) ?></td>
                                        <td><?= htmlspecialchars(trim(((string) ($item['sessionDate'] ?? '')) . ' ' . ((string) ($item['startTime'] ?? '')))) ?></td>
                                        <td><?= (int) ($item['quantity'] ?? 0) ?></td>
                                        <td>EUR <?= number_format((float) ($item['unitPrice'] ?? 0), 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="p-4 border-bottom border-secondary-subtle">
                    <h2 class="h5 mb-1">Created Tickets</h2>
                    <p class="text-muted mb-0">Tickets created after payment.</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Ticket</th>
                                <th>Event</th>
                                <th>Session</th>
                                <th>Venue</th>
                                <th>Date</th>
                                <th>Ticket Status</th>
                                <th>QR</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($tickets === []): ?>
                                <tr>
                                    <td colspan="8" class="cms-empty-state">No issued tickets found for this order.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($tickets as $ticket): ?>
                                    <tr>
                                        <td>#<?= (int) ($ticket['ticketId'] ?? 0) ?></td>
                                        <td><?= htmlspecialchars((string) ($ticket['eventName'] ?? 'Unknown')) ?></td>
                                        <td><?= htmlspecialchars((string) (($ticket['sessionLabel'] ?? '') !== '' ? $ticket['sessionLabel'] : ('Session #' . (int) ($ticket['sessionId'] ?? 0)))) ?></td>
                                        <td><?= htmlspecialchars((string) ($ticket['venueName'] ?? 'Unknown venue')) ?></td>
                                        <td><?= htmlspecialchars(trim(((string) ($ticket['sessionDate'] ?? '')) . ' ' . ((string) ($ticket['startTime'] ?? '')))) ?></td>
                                        <td><?= htmlspecialchars((string) ($ticket['ticketStatus'] ?? 'Unknown')) ?></td>
                                        <td><?= htmlspecialchars(((string) ($ticket['qrCode'] ?? '')) !== '' ? 'Ready' : 'Pending') ?></td>
                                        <td class="text-end">
                                            <?php if (((string) ($ticket['qrCode'] ?? '')) !== ''): ?>
                                                <a href="/cms/tickets/qr?ticket_id=<?= (int) ($ticket['ticketId'] ?? 0) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">Open QR</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
