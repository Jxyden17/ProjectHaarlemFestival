<div class="container py-4">
    <div class="mb-3">
        <h1 class="h3 mb-1">Event Management</h1>
        <p class="text mb-0">Choose an event and open Artists and Venues.</p>
    </div>

    <div class="mb-3">
        <a href="/cms" class="btn btn-sm btn-outline-secondary">Back to CMS</a>
    </div>

    <div class="row g-3">
        <?php foreach (($events ?? []) as $event): ?>
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h5 mb-0"><?= htmlspecialchars((string)($event['label'] ?? 'Event')) ?></h2>
                            <span class="badge text-bg-success">Live</span>
                        </div>
                        <div class="d-grid gap-2 d-md-flex flex-wrap">
                            <?php if (!empty($event['supportsArtists'])): ?>
                                <a href="/cms/eventManagement/artists?event_id=<?= $event['id'] ?? 0 ?>" class="btn btn-outline-primary">Artists</a>
                            <?php endif; ?>
                            <a href="/cms/eventManagement/venues?event_id=<?= $event['id'] ?? 0 ?>" class="btn btn-outline-primary">Venues</a>
                            <?php if (!empty($event['supportsTickets'])): ?>
                                <?php if (($event['slug'] ?? '') === 'dance'): ?>
                                    <a href="/cms/eventManagement/dance/schedule-editor" class="btn btn-outline-primary">Schedule Editor</a>
                                <?php else: ?>
                                    <a href="/cms/eventManagement/schedules?event_id=<?= $event['id'] ?? 0 ?>" class="btn btn-outline-primary">Schedule</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
