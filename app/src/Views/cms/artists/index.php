<?php
use App\Models\Enums\Event;

$selectedEvent = $selectedEvent ?? Event::Tour;
$eventSlug = strtolower($selectedEvent->label());
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Artist Management - <?= htmlspecialchars($selectedEvent->label()) ?></h1>
        <a href="/cms/eventManagement/artists/create?event=<?= rawurlencode($eventSlug) ?>" class="btn btn-primary">Add Artist</a>
    </div>

    <div class="mb-3">
        <a href="/cms" class="btn btn-sm btn-outline-secondary">Back to CMS</a>
        <a href="/cms/eventManagement" class="btn btn-sm btn-outline-secondary">Events</a>
        <a href="/cms/eventManagement/tickets?event=<?= rawurlencode($eventSlug) ?>" class="btn btn-sm btn-outline-secondary">Tickets</a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Profesion</th>
                    <th>Event</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $counter = 1; ?>
                <?php foreach ($artistes as $artiste): ?>
                    <tr>
                        <td><?= $counter++ ?></td>
                        <td><?= htmlspecialchars($artiste->performerName ?? 'Unknown') ?></td>
                        <td><?= htmlspecialchars($artiste->performerType ?? 'Unknown') ?></td>
                        <td><?= htmlspecialchars(Event::from($artiste->eventId)->label() ?? 'Unknown') ?></td>
                        <td><?= htmlspecialchars($artiste->description ?? 'No description') ?></td>
                        <td>
                            <a href="/cms/eventManagement/artists/edit?id=<?= (int)$artiste->id ?>&event=<?= rawurlencode($eventSlug) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <a href="/cms/eventManagement/artists/delete?id=<?= (int)$artiste->id ?>&event=<?= rawurlencode($eventSlug) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this artist? This cannot be undone.')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
