<?php
$summary = is_array($summary ?? null) ? $summary : [];
$eventCards = is_array($events ?? null) ? $events : [];

$ticketedEventCount = (int) ($summary['ticketedEventCount'] ?? 0);
$sessionCount = (int) ($summary['sessionCount'] ?? 0);
$capacityTotal = (int) ($summary['capacityTotal'] ?? 0);
$availableTotal = (int) ($summary['availableTotal'] ?? 0);
$soldTotal = (int) ($summary['soldTotal'] ?? 0);
$issuedTicketCount = (int) ($summary['issuedTicketCount'] ?? 0);
$pendingPayments = (int) ($summary['pendingPayments'] ?? 0);
$paidPayments = (int) ($summary['paidPayments'] ?? 0);
$failedPayments = (int) ($summary['failedPayments'] ?? 0);
?>

<div class="container-lg py-4 py-md-5">
    <div class="vstack gap-3">
        <section class="cms-page-hero">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <p class="cms-page-hero__eyebrow">Operational control</p>
                    <h1 class="cms-page-hero__title">Ticket Management</h1>
                    <p class="cms-page-hero__description">Monitor capacity, sales, and payment pressure from one place.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/cms" class="btn btn-outline-secondary">Back to CMS</a>
                    <a href="/cms/eventManagement" class="btn btn-outline-secondary">Event Management</a>
                </div>
            </div>
        </section>

        <section class="card border-0 shadow-sm cms-ticket-focus-strip">
            <div class="card-body p-3 p-md-4">
                <div class="cms-ticket-overview-grid">
                    <div class="cms-ticket-overview-main">
                        <span class="cms-ticket-overview-main__eyebrow">Overview</span>
                        <div class="cms-ticket-overview-main__stats">
                            <div class="cms-ticket-overview-main__stat">
                                <span class="cms-ticket-overview-main__label">Events</span>
                                <strong class="cms-ticket-overview-main__value"><?= $ticketedEventCount ?></strong>
                            </div>
                            <div class="cms-ticket-overview-main__stat">
                                <span class="cms-ticket-overview-main__label">Capacity</span>
                                <strong class="cms-ticket-overview-main__value"><?= $capacityTotal ?></strong>
                            </div>
                            <div class="cms-ticket-overview-main__stat">
                                <span class="cms-ticket-overview-main__label">Tickets sold</span>
                                <strong class="cms-ticket-overview-main__value"><?= $soldTotal ?></strong>
                            </div>
                            <div class="cms-ticket-overview-main__stat">
                                <span class="cms-ticket-overview-main__label">Available</span>
                                <strong class="cms-ticket-overview-main__value"><?= $availableTotal ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="cms-ticket-overview-side">
                        <div class="cms-ticket-overview-side__alert">
                            <span class="cms-ticket-overview-side__label">Need attention</span>
                            <strong class="cms-ticket-overview-side__value"><?= $pendingPayments ?></strong>
                            <span class="cms-ticket-overview-side__meta">Pending payments</span>
                        </div>
                        <div class="cms-ticket-overview-side__meta-grid">
                            <div class="cms-ticket-overview-side__meta-card">
                                <span class="cms-ticket-overview-side__meta-label">Issued</span>
                                <strong class="cms-ticket-overview-side__meta-value"><?= $issuedTicketCount ?></strong>
                            </div>
                            <div class="cms-ticket-overview-side__meta-card">
                                <span class="cms-ticket-overview-side__meta-label">Capacity</span>
                                <strong class="cms-ticket-overview-side__meta-value"><?= $capacityTotal ?></strong>
                            </div>
                        </div>
                        <div class="cms-ticket-status-inline">
                            <span class="cms-ticket-status-inline__label">Payments</span>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2">Paid <?= $paidPayments ?></span>
                                <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2">Pending <?= $pendingPayments ?></span>
                                <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2">Failed <?= $failedPayments ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="row g-3 g-md-4">
            <?php foreach ($eventCards as $event): ?>
                <div class="col-12 col-lg-6">
                    <div class="card h-100 border-0 shadow-sm cms-ticket-event-card">
                        <div class="card-body p-4">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <h2 class="h5 mb-0"><?= htmlspecialchars((string) ($event['label'] ?? 'Event')) ?></h2>
                                </div>
                                <div class="d-flex flex-wrap gap-2 justify-content-end">
                                    <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2">
                                        Issued <?= (int) ($event['issuedTicketCount'] ?? 0) ?>
                                    </span>
                                    <?php if ((int) ($event['unlimitedSessions'] ?? 0) > 0): ?>
                                        <span class="badge rounded-pill text-bg-light">Unlimited <?= (int) $event['unlimitedSessions'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="cms-ticket-event-metrics mb-4">
                                <div class="cms-ticket-event-metric">
                                    <span class="cms-ticket-event-metric__label">Sold</span>
                                    <strong class="cms-ticket-event-metric__value"><?= (int) ($event['soldTotal'] ?? 0) ?></strong>
                                </div>
                                <div class="cms-ticket-event-metric">
                                    <span class="cms-ticket-event-metric__label">Available</span>
                                    <strong class="cms-ticket-event-metric__value"><?= (int) ($event['availableTotal'] ?? 0) ?></strong>
                                </div>
                                <div class="cms-ticket-event-metric">
                                    <span class="cms-ticket-event-metric__label">Capacity</span>
                                    <strong class="cms-ticket-event-metric__value"><?= (int) ($event['capacityTotal'] ?? 0) ?></strong>
                                </div>
                                <div class="cms-ticket-event-metric">
                                    <span class="cms-ticket-event-metric__label">Issued</span>
                                    <strong class="cms-ticket-event-metric__value"><?= (int) ($event['issuedTicketCount'] ?? 0) ?></strong>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-sm-flex">
                                <a href="/cms/tickets/orders?event_id=<?= (int) ($event['id'] ?? 0) ?>" class="btn btn-outline-secondary">View Orders</a>
                                <a href="/cms/tickets/sold?event_id=<?= (int) ($event['id'] ?? 0) ?>" class="btn btn-primary">View Sold Tickets</a>
                                <a href="/cms/eventManagement/schedules?event_id=<?= (int) ($event['id'] ?? 0) ?>" class="btn btn-outline-secondary">Manage Availability</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
