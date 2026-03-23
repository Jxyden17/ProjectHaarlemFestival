<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Ticket Management - <?= htmlspecialchars($selectedEvent->label()) ?></h1>
        <a href="/cms/eventManagement/schedules/create?event_id=<?= $selectedEvent->value ?>" class="btn btn-primary btn-sm">+ Add Schedule</a>
    </div>

    <form method="GET" action="/cms/eventManagement/schedules" class="row g-2 align-items-end mb-3">
        <div class="col-sm-6 col-md-4">
            <label for="event-switch" class="form-label">Select Event</label>
            <select id="event-switch" name="event_id" class="form-select">
                <?php foreach ($eventTypes as $event): ?>
                    <option value="<?= htmlspecialchars($event->value) ?>"<?= $event->value === $selectedEvent->value ? ' selected' : '' ?>>
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
                    <th>Event</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Aantal Tickets</th>
                    <th>Verkocht Tickets</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($schedules) || empty($schedules->sessions)): ?>
                    <tr>
                        <td colspan="6" class="text-muted">No ticket moments found for this event.</td>
                    </tr>
                <?php else: ?>
                    <?php $counter = 1; ?>
                    <?php foreach ($schedules->sessions as $row): ?>
                        <tr>
                            <input type="hidden" name="id" value="<?= $row->id ?>">
                            <td><?= $counter++ ?></td>
                            <td><?= htmlspecialchars($row->event->name) ?></td>
                            <td><?= htmlspecialchars($row->date ?? 'Unknown') ?></td>
                            <td><?= htmlspecialchars($row->startTime ?? 'Unknown') ?></td>
                            <td><?= ($row->availableSpots ?? 0) ?></td>
                            <td><?= ($row->amountSold ?? 0) ?></td>
                            <td>
                                <a href="/cms/eventManagement/schedules/edit?event_id=<?= $selectedEvent->value ?>&id=<?= $row->id ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="/cms/eventManagement/schedules/view?event_id=<?= $selectedEvent->value ?>&id=<?= $row->id ?>" class="btn btn-sm btn-outline-primary">View Tickets</a>
                                <a href="/cms/eventManagement/schedules/delete?event_id=<?= $selectedEvent->value ?>&id=<?= $row->id ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete ticket slot #<?= $counter ?>?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
