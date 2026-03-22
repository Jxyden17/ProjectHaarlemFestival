<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Ticket Management - <?= htmlspecialchars($selectedEvent->label()) ?></h1>
        <a href="/cms/eventManagement/tickets/create?event_id=<?= $selectedEvent->value ?>" class="btn btn-primary btn-sm">+ Add Tickets</a>
    </div>

    <form method="GET" action="/cms/eventManagement/tickets" class="row g-2 align-items-end mb-3">
        <div class="col-sm-6 col-md-4">
            <label for="event-switch" class="form-label">Select Event</label>
            <select id="event-switch" name="event" class="form-select">
                <?php foreach ($eventTypes as $event): ?>
                    <?php $slug = $event->value; ?>
                    <option value="<?= htmlspecialchars($slug) ?>"<?= $slug === $eventid ? ' selected' : '' ?>>
                        <?= htmlspecialchars($event->label()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-6 col-md-2">
            <button type="submit" class="btn btn-outline-secondary w-100">Switch</button>
        </div>
    </form>

    <div class="mb-3">
        <a href="/cms" class="btn btn-sm btn-outline-secondary">Back to CMS</a>
        <a href="/cms/eventManagement" class="btn btn-sm btn-outline-secondary">Events</a>
        <a href="/cms/eventManagement/artists?event_id=<?= $selectedEvent->value ?>" class="btn btn-sm btn-outline-secondary">Artists</a>
        <a href="/cms/eventManagement/venues?event_id=<?= $selectedEvent->value ?>" class="btn btn-sm btn-outline-secondary">Venues</a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Aantal te verkopen</th>
                    <th>Verkocht</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($tickets === []): ?>
                    <tr>
                        <td colspan="6" class="text-muted">No ticket moments found for this event.</td>
                    </tr>
                <?php else: ?>
                    <?php $counter = 1; ?>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td><?= $counter++ ?></td>
                            <td><?= htmlspecialchars($ticket->date ?? 'Unknown') ?></td>
                            <td><?= htmlspecialchars($ticket->startTime ?? 'Unknown') ?></td>
                            <td><?= (int) ($ticket->availableSpots ?? 0) ?></td>
                            <td><?= (int) ($ticket->amountSold ?? 0) ?></td>
                            <td>
                                <a href="/cms/tickets/edit?event_id=<?= $selectedEvent->value ?>&id=<?= $ticket->id ?>"
                                   class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="/cms/tickets/delete?event_id=<?= $selectedEvent->value ?>&id=<?= $ticket->id ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Delete ticket slot #<?= $counter ?>?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
