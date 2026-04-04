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
                <div class="row g-3 align-items-stretch">
                    <div class="col-12 col-md-4">
                        <div class="cms-ticket-focus-card">
                            <span class="cms-ticket-focus-card__label">Need attention</span>
                            <strong class="cms-ticket-focus-card__value"><?= $pendingPayments ?></strong>
                            <span class="cms-ticket-focus-card__meta">Pending payments</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="cms-ticket-mini-stat">
                            <span class="cms-ticket-mini-stat__label">Events</span>
                            <strong class="cms-ticket-mini-stat__value"><?= $ticketedEventCount ?></strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="cms-ticket-mini-stat">
                            <span class="cms-ticket-mini-stat__label">Sessions</span>
                            <strong class="cms-ticket-mini-stat__value"><?= $sessionCount ?></strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="cms-ticket-mini-stat">
                            <span class="cms-ticket-mini-stat__label">Sold</span>
                            <strong class="cms-ticket-mini-stat__value"><?= $soldTotal ?></strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="cms-ticket-mini-stat">
                            <span class="cms-ticket-mini-stat__label">Available</span>
                            <strong class="cms-ticket-mini-stat__value"><?= $availableTotal ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="row g-3">
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <p class="text-uppercase text-muted small fw-semibold mb-2">Issued tickets</p>
                        <h2 class="display-6 mb-1"><?= $issuedTicketCount ?></h2>
                        <p class="text-muted mb-0">Tickets generated after successful payment.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <p class="text-uppercase text-muted small fw-semibold mb-2">Total capacity</p>
                        <h2 class="display-6 mb-1"><?= $capacityTotal ?></h2>
                        <p class="text-muted mb-0">Configured spots across ticketed sessions.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <p class="text-uppercase text-muted small fw-semibold mb-2">Payment status</p>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2">Paid <?= $paidPayments ?></span>
                            <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2">Pending <?= $pendingPayments ?></span>
                            <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2">Failed <?= $failedPayments ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 g-md-4">
            <?php foreach ($eventCards as $event): ?>
                <div class="col-12 col-lg-6">
                    <div class="card h-100 border-0 shadow-sm cms-ticket-event-card">
                        <div class="card-body p-4">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                                <div>
                                    <p class="text-uppercase text-muted small fw-semibold mb-1">Event</p>
                                    <h2 class="h5 mb-0"><?= htmlspecialchars((string) ($event['label'] ?? 'Event')) ?></h2>
                                </div>
                                <?php if ((int) ($event['unlimitedSessions'] ?? 0) > 0): ?>
                                    <span class="badge rounded-pill text-bg-light">Unlimited <?= (int) $event['unlimitedSessions'] ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="row g-2 mb-4">
                                <div class="col-6">
                                    <div class="cms-stat-chip">
                                        <span class="cms-stat-chip__label">Sessions</span>
                                        <strong class="cms-stat-chip__value"><?= (int) ($event['sessionCount'] ?? 0) ?></strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="cms-stat-chip">
                                        <span class="cms-stat-chip__label">Capacity</span>
                                        <strong class="cms-stat-chip__value"><?= (int) ($event['capacityTotal'] ?? 0) ?></strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="cms-stat-chip">
                                        <span class="cms-stat-chip__label">Sold</span>
                                        <strong class="cms-stat-chip__value"><?= (int) ($event['soldTotal'] ?? 0) ?></strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="cms-stat-chip">
                                        <span class="cms-stat-chip__label">Available</span>
                                        <strong class="cms-stat-chip__value"><?= (int) ($event['availableTotal'] ?? 0) ?></strong>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <div class="text-muted small">Issued tickets: <?= (int) ($event['issuedTicketCount'] ?? 0) ?></div>
                                <a href="/cms/eventManagement/schedules?event_id=<?= (int) ($event['id'] ?? 0) ?>" class="btn btn-primary">Manage Availability</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
