<?php
$selectedEvent = $selectedEvent ?? \App\Models\Enums\Event::Tour;
$eventSlug = strtolower($selectedEvent->label());
$venues = is_array($venues ?? null) ? $venues : [];
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Venue Management - <?= htmlspecialchars($selectedEvent->label()) ?></h1>
        <a href="/cms/eventManagement/venues/create?event=<?= rawurlencode($eventSlug) ?>" class="btn btn-primary btn-sm">+ Add Venue</a>
    </div>

    <div class="mb-3">
        <a href="/cms" class="btn btn-sm btn-outline-secondary">Back to CMS</a>
        <a href="/cms/eventManagement" class="btn btn-sm btn-outline-secondary">Events</a>
        <a href="/cms/eventManagement/artists?event=<?= rawurlencode($eventSlug) ?>" class="btn btn-sm btn-outline-secondary">Artists</a>
        <a href="/cms/eventManagement/tickets?event=<?= rawurlencode($eventSlug) ?>" class="btn btn-sm btn-outline-secondary">Tickets</a>
    </div>

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
                <?php if ($venues === []): ?>
                    <tr>
                        <td colspan="4" class="text-muted">No venues found for this event.</td>
                    </tr>
                <?php else: ?>
                    <?php $counter = 1; ?>
                    <?php foreach ($venues as $venue): ?>
                        <tr>
                            <td><?= $counter++ ?></td>
                            <td><?= htmlspecialchars($venue->venueName ?? '') ?></td>
                            <td><?= htmlspecialchars($venue->address ?? '') ?></td>
                            <td><?= htmlspecialchars($venue->venueType ?? 'Unknown') ?></td>
                            <td>
                                <a href="/cms/eventManagement/venues/edit?event=<?= rawurlencode($eventSlug) ?>&venueId=<?= rawurlencode((string)($venue->id ?? '')) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="/cms/eventManagement/venues/delete?event=<?= rawurlencode($eventSlug) ?>&venueId=<?= rawurlencode((string)($venue->id ?? '')) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this venue?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
