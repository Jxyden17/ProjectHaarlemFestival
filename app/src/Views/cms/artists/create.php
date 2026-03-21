<?php
$eventTypes = \App\Models\Enums\Event::cases();
$selectedEvent = $selectedEvent ?? \App\Models\Enums\Event::Tour;
$eventSlug = strtolower($selectedEvent->label());
?>
<div class="container py-4">
    <h1 class="h3 mb-3">Create Artist</h1>
    <div class="mb-3">
        <a href="/cms/eventManagement/artists?event=<?= rawurlencode($eventSlug) ?>" class="btn btn-sm btn-outline-secondary">Back to Artists</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars((string)$error) ?></div>
    <?php endif; ?>

    <form action="/cms/eventManagement/artists/create?event=<?= rawurlencode($eventSlug) ?>" method="POST" class="card p-3">
        <div class="mb-3">
            <label for="performer_name" class="form-label">Name</label>
            <input type="text" id="performer_name" name="performer_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="performer_type" class="form-label">Profession</label>
            <input type="text" id="performer_type" name="performer_type" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="event_id" class="form-label">Select Event</label>
            <select id="event_id" name="event_id" class="form-select" required>
                
            <?php foreach ($eventTypes as $event): ?>
                <option value="<?= $event->value ?>"<?= $selectedEvent->value === $event->value ? ' selected' : '' ?>>
                <?= htmlspecialchars($event->label()) ?> 
            </option>
            <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <input type="text" id="description" name="description" class="form-control">
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Create Artist</button>
            <a href="/cms/eventManagement/artists?event=<?= rawurlencode($eventSlug) ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
