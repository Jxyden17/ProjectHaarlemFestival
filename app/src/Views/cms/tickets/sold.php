<?php

use App\Models\Enums\Event;

$selectedEvent = ($selectedEvent ?? null) instanceof Event ? $selectedEvent : Event::Dance;
$eventTypes = is_array($eventTypes ?? null) ? $eventTypes : [];
$tickets = is_array($tickets ?? null) ? $tickets : [];
$summary = is_array($summary ?? null) ? $summary : [];
$paymentStatusFilter = is_string($paymentStatusFilter ?? null) ? $paymentStatusFilter : 'all';

$ticketCount = (int) ($summary['ticketCount'] ?? 0);
$paidCount = (int) ($summary['paidCount'] ?? 0);
$pendingCount = (int) ($summary['pendingCount'] ?? 0);
?>

<div class="container-lg py-4 py-md-5">
    <div class="vstack gap-3">
        <section class="cms-page-hero">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <p class="cms-page-hero__eyebrow">Ticket operations</p>
                    <h1 class="cms-page-hero__title">Sold Tickets</h1>
                    <p class="cms-page-hero__description">Review issued tickets per event, including payment state, order reference, and session details.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/cms/tickets" class="btn btn-outline-secondary">Back to Ticket Hub</a>
                </div>
            </div>
        </section>

        <section class="card border-0 shadow-sm">
            <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <form method="GET" action="/cms/tickets/sold" class="row g-2 align-items-end m-0">
                    <div class="col-12 col-md-auto">
                        <label for="ticket-event-switch" class="form-label mb-1">Select Event</label>
                        <select id="ticket-event-switch" name="event_id" class="form-select">
                            <?php foreach ($eventTypes as $event): ?>
                                <?php if (!$event instanceof Event) { continue; } ?>
                                <option value="<?= htmlspecialchars((string) $event->value) ?>"<?= $event->value === $selectedEvent->value ? ' selected' : '' ?>>
                                    <?= htmlspecialchars($event->label()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-auto">
                        <label for="ticket-payment-filter" class="form-label mb-1">Payment</label>
                        <select id="ticket-payment-filter" name="payment_status" class="form-select">
                            <option value="all"<?= $paymentStatusFilter === 'all' ? ' selected' : '' ?>>All</option>
                            <option value="paid"<?= $paymentStatusFilter === 'paid' ? ' selected' : '' ?>>Paid</option>
                            <option value="pending"<?= $paymentStatusFilter === 'pending' ? ' selected' : '' ?>>Pending</option>
                            <option value="failed"<?= $paymentStatusFilter === 'failed' ? ' selected' : '' ?>>Failed</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">View Tickets</button>
                    </div>
                </form>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2"><?= $ticketCount ?> tickets</span>
                    <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2">Paid <?= $paidCount ?></span>
                    <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2">Pending <?= $pendingCount ?></span>
                    <a href="/cms/eventManagement/schedules?event_id=<?= $selectedEvent->value ?>" class="btn btn-outline-secondary">Manage Availability</a>
                </div>
            </div>
        </section>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Ticket</th>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Session</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>QR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($tickets === []): ?>
                                <tr>
                                    <td colspan="9" class="cms-empty-state">No sold tickets found for <?= htmlspecialchars($selectedEvent->label()) ?>.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($tickets as $ticket): ?>
                                    <tr>
                                        <td>#<?= (int) ($ticket['ticketId'] ?? 0) ?></td>
                                        <td>#<?= (int) ($ticket['orderId'] ?? 0) ?></td>
                                        <td><?= htmlspecialchars((string) ($ticket['customerEmail'] ?? 'Unknown')) ?></td>
                                        <td><?= htmlspecialchars((string) (($ticket['sessionLabel'] ?? '') !== '' ? $ticket['sessionLabel'] : ('Session #' . (int) ($ticket['sessionId'] ?? 0)))) ?></td>
                                        <td><?= htmlspecialchars((string) ($ticket['sessionDate'] ?? '')) ?></td>
                                        <td><?= htmlspecialchars((string) ($ticket['startTime'] ?? '')) ?></td>
                                        <td>
                                            <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2">
                                                <?= htmlspecialchars(ucfirst((string) ($ticket['paymentStatus'] ?? 'unknown'))) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars((string) ($ticket['ticketStatus'] ?? 'Unknown')) ?></td>
                                        <td><?= htmlspecialchars((string) (($ticket['qrCode'] ?? '') !== '' ? 'Available' : 'Pending')) ?></td>
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
