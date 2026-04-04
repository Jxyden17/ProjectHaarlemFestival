<?php

use App\Models\Enums\Event;

$selectedEvent = ($selectedEvent ?? null) instanceof Event ? $selectedEvent : null;
$eventTypes = is_array($eventTypes ?? null) ? $eventTypes : [];
$orders = is_array($orders ?? null) ? $orders : [];
$summary = is_array($summary ?? null) ? $summary : [];
$paymentStatusFilter = is_string($paymentStatusFilter ?? null) ? $paymentStatusFilter : 'all';

$orderCount = (int) ($summary['orderCount'] ?? 0);
$paidCount = (int) ($summary['paidCount'] ?? 0);
$pendingCount = (int) ($summary['pendingCount'] ?? 0);
$failedCount = (int) ($summary['failedCount'] ?? 0);
$grossAmount = (float) ($summary['grossAmount'] ?? 0);
?>

<div class="container-lg py-4 py-md-5">
    <div class="vstack gap-3">
        <section class="cms-page-hero">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <p class="cms-page-hero__eyebrow">Ticket Management</p>
                    <h1 class="cms-page-hero__title">Orders</h1>
                    <p class="cms-page-hero__description">Orders overview.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/cms/tickets" class="btn btn-outline-secondary">Back to Ticket Hub</a>
                </div>
            </div>
        </section>

        <section class="card border-0 shadow-sm">
            <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <form method="GET" action="/cms/tickets/orders" class="row g-2 align-items-end m-0">
                    <div class="col-12 col-md-auto">
                        <label for="ticket-order-event-switch" class="form-label mb-1">Event</label>
                        <select id="ticket-order-event-switch" name="event_id" class="form-select">
                            <option value="">All events</option>
                            <?php foreach ($eventTypes as $event): ?>
                                <?php if (!$event instanceof Event) { continue; } ?>
                                <option value="<?= htmlspecialchars((string) $event->value) ?>"<?= $selectedEvent !== null && $event->value === $selectedEvent->value ? ' selected' : '' ?>>
                                    <?= htmlspecialchars($event->label()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-auto">
                        <label for="ticket-order-payment-filter" class="form-label mb-1">Payment</label>
                        <select id="ticket-order-payment-filter" name="payment_status" class="form-select">
                            <option value="all"<?= $paymentStatusFilter === 'all' ? ' selected' : '' ?>>All</option>
                            <option value="paid"<?= $paymentStatusFilter === 'paid' ? ' selected' : '' ?>>Paid</option>
                            <option value="pending"<?= $paymentStatusFilter === 'pending' ? ' selected' : '' ?>>Pending</option>
                            <option value="failed"<?= $paymentStatusFilter === 'failed' ? ' selected' : '' ?>>Failed</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Apply</button>
                    </div>
                </form>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2"><?= $orderCount ?> orders</span>
                    <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2">Paid <?= $paidCount ?></span>
                    <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2">Pending <?= $pendingCount ?></span>
                    <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2">Failed <?= $failedCount ?></span>
                    <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2">Gross EUR <?= number_format($grossAmount, 2) ?></span>
                </div>
            </div>
        </section>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Events</th>
                                <th>Total</th>
                                <th>Tickets</th>
                                <th>Payment</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders === []): ?>
                                <tr>
                                    <td colspan="8" class="cms-empty-state">No orders found for this filter.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <?php
                                        $expectedTicketCount = (int) ($order['expectedTicketCount'] ?? 0);
                                        $issuedTicketCount = (int) ($order['issuedTicketCount'] ?? 0);
                                        $eventNames = trim((string) ($order['eventNames'] ?? ''));
                                        $paymentStatus = ucfirst((string) ($order['paymentStatus'] ?? 'unknown'));
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">#<?= (int) ($order['orderId'] ?? 0) ?></div>
                                        </td>
                                        <td><?= htmlspecialchars((string) ($order['customerEmail'] ?? 'Unknown')) ?></td>
                                        <td>
                                            <div><?= htmlspecialchars($eventNames !== '' ? $eventNames : 'No event summary') ?></div>
                                        </td>
                                        <td>EUR <?= number_format((float) ($order['totalAmount'] ?? 0), 2) ?></td>
                                        <td>
                                            <div><?= $expectedTicketCount ?> ordered</div>
                                        </td>
                                        <td><?= htmlspecialchars($paymentStatus) ?></td>
                                        <td><?= htmlspecialchars((string) ($order['createdAt'] ?? '')) ?></td>
                                        <td class="text-end">
                                            <a href="/cms/tickets/orders/detail?order_id=<?= (int) ($order['orderId'] ?? 0) ?>" class="btn btn-sm btn-outline-primary">View</a>
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
