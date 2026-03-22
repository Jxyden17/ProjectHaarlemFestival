<div class="container py-4">
    <h1 class="h3 mb-3">Edit Artiste - <?= htmlspecialchars($artiste->performerName) ?></h1>
    <div class="mb-3">
        <a href="/cms/eventManagement/artists?event_id=<?= $selectedEvent->value ?>" class="btn btn-sm btn-outline-secondary">Back to Artists</a>
    </div>

    <form action="/cms/eventManagement/artists/edit?event_id=<?= $selectedEvent->value ?>" method="POST" class="card p-3">
        <input type="hidden" name="id" value="<?= (int)$artiste->id ?>">

        <div class="mb-3">
            <label for="performer_name" class="form-label">Name</label>
            <input type="text" id="performer_name" name="performer_name" class="form-control" value="<?= htmlspecialchars($artiste->performerName) ?>" required>
        </div>

        <div class="mb-3">
            <label for="performer_type" class="form-label">Profession</label>
            <input type="text" id="performer_type" name="performer_type" class="form-control" value="<?= $artiste->performerType ?>" required>
        </div>

        <div class="mb-3">
            <label for="event_id" class="form-label">Event</label>
            <select id="event_id" name="event_id" class="form-select">
                <?php foreach ($eventTypes as $event): ?>
                    <option value="<?= $event->value ?>"<?= $artiste->eventId === $event->value ? 'selected' : '' ?>>
                    <?= htmlspecialchars($event->label()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <input type="text" id="description" name="description" class="form-control" value="<?= $artiste->description ?>" required>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="/cms/eventManagement/artists?event_id=<?= $selectedEvent->value ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
