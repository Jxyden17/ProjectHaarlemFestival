<?php $eventCards = is_array($events ?? null) ? $events : []; ?>
<div class="container-lg py-4 py-md-5">
    <div class="vstack gap-3">
        <section class="cms-page-hero">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <p class="cms-page-hero__eyebrow">Operational control</p>
                    <h1 class="cms-page-hero__title">Event Management</h1>
                    <p class="cms-page-hero__description">Choose an event and open the operational sections that control artists, venues, and schedules.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/cms" class="btn btn-outline-secondary">Back to CMS</a>
                </div>
            </div>
        </section>

        <div class="row g-3 g-md-4">
            <?php foreach ($eventCards as $event): ?>
                <?php
                $supportsArtists = !empty($event['supportsArtists']);
                $supportsTickets = !empty($event['supportsTickets']);
                ?>
                <div class="col-12 col-lg-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="mb-4">
                                <div>
                                    <p class="text-uppercase text-muted small fw-semibold mb-2">Event hub</p>
                                    <h2 class="h5 mb-2"><?= htmlspecialchars((string)($event['label'] ?? 'Event')) ?></h2>
                                    <p class="text-muted mb-0">Jump into the operational modules configured for this event.</p>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex flex-wrap">
                                <?php if ($supportsArtists): ?>
                                    <a href="/cms/eventManagement/artists?event_id=<?= $event['id'] ?? 0 ?>" class="btn btn-primary">Artists</a>
                                <?php endif; ?>
                                <a href="/cms/eventManagement/venues?event_id=<?= $event['id'] ?? 0 ?>" class="btn btn-outline-primary">Venues</a>
                                <?php if ($supportsTickets): ?>
                                    <a href="/cms/eventManagement/schedules?event_id=<?= $event['id'] ?? 0 ?>" class="btn btn-outline-primary">Schedules</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
