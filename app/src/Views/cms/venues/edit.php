<div class="container py-4">
    <h1 class="h3 mb-3">Edit Venue - <?= htmlspecialchars($venue->venueName) ?></h1>
    <div class="mb-3">
        <a href="/cms/eventManagement/venues?event_id=<?= $selectedEvent->value ?>" class="btn btn-sm btn-outline-secondary">Back to Venues</a>
    </div>

    <form action="/cms/eventManagement/venues/edit?event_id=<?= $selectedEvent->value ?>" method="POST" class="card p-3">
        <input type="hidden" name="id" value="<?= $venue->id ?>">

        <div class="mb-3">
            <label for="venue_name" class="form-label">Venue Name <span class="text-danger">*</span></label>
            <input type="text" id="venue_name" name="venue_name" class="form-control"
                   value="<?= htmlspecialchars($venue->venueName) ?>" required>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" id="address" name="address" class="form-control"
                   value="<?= htmlspecialchars($venue->address ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="venue_type" class="form-label">Type</label>
            <input type="text" id="venue_type" name="venue_type" class="form-control"
                   value="<?= htmlspecialchars($venue->venueType ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="event_id" class="form-label">Event</label>
            <select id="event_id" name="event_id" class="form-select">
                <?php foreach ($eventTypes as $event): ?>
                    <option value="<?= $event->value ?>"<?= $venue->eventId === $event->value ? 'selected' : '' ?>>
                    <?= htmlspecialchars($event->label()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="/cms/eventManagement/venues?event_id=<?= $selectedEvent->value ?>&id=<?= $venue->id ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
