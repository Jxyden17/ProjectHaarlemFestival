<?php $venueRows = is_array($venues ?? null) ? $venues : []; ?>
<div class="container-lg py-4 py-md-5">
    <div class="vstack gap-3">
        <section class="cms-page-hero">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <p class="cms-page-hero__eyebrow">Event operations</p>
                    <h1 class="cms-page-hero__title">Venue Management</h1>
                    <p class="cms-page-hero__description">Maintain venue records for <?= htmlspecialchars($selectedEvent->label()) ?>, including addresses, types, and linked event data.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/cms/eventManagement" class="btn btn-outline-secondary">Back to Events</a>
                    <a href="/cms/eventManagement/venues/create?event_id=<?= $selectedEvent->value ?>" class="btn btn-primary">Add Venue</a>
                </div>
            </div>
        </section>

        <section class="card border-0 shadow-sm">
            <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2"><?= htmlspecialchars($selectedEvent->label()) ?></span>
                    <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2"><?= count($venueRows) ?> venues</span>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="/cms/eventManagement/artists?event_id=<?= $selectedEvent->value ?>" class="btn btn-outline-secondary">Artists</a>
                    <a href="/cms/eventManagement/schedules?event_id=<?= $selectedEvent->value ?>" class="btn btn-outline-secondary">Schedules</a>
                </div>
            </div>
        </section>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($venueRows === []): ?>
                                <tr>
                                    <td colspan="5" class="cms-empty-state">No venues found for this event.</td>
                                </tr>
                            <?php else: ?>
                                <?php $counter = 1; ?>
                                <?php foreach ($venueRows as $venue): ?>
                                    <tr>
                                        <td><?= $counter++ ?></td>
                                        <td><?= htmlspecialchars($venue->venueName ?? '') ?></td>
                                        <td><?= htmlspecialchars($venue->address ?? '') ?></td>
                                        <td><?= htmlspecialchars($venue->venueType ?? 'Unknown') ?></td>
                                        <td>
                                            <div class="cms-table-actions">
                                                <a href="/cms/eventManagement/venues/edit?event_id=<?= $selectedEvent->value ?>&id=<?= $venue->id ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <a href="/cms/eventManagement/venues/delete?event_id=<?= $selectedEvent->value ?>&id=<?= $venue->id  ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this venue?');">Delete</a>
                                            </div>
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
