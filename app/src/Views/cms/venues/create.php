<?php
$selectedEvent = $selectedEvent ?? \App\Models\Enums\Event::Tour;
$eventSlug = strtolower($selectedEvent->label());
?>
<div class="container py-4">
    <h1 class="h3 mb-3">Create Venue - <?= htmlspecialchars($selectedEvent->label()) ?></h1>
    <div class="mb-3">
        <a href="/cms/eventManagement/venues?event=<?= rawurlencode($eventSlug) ?>" class="btn btn-sm btn-outline-secondary">Back to Venues</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars((string) $error) ?></div>
    <?php endif; ?>

    <form action="/cms/eventManagement/venues/create?event=<?= rawurlencode($eventSlug) ?>" method="POST" class="card p-3">
        <div class="mb-3">
            <label for="venue_name" class="form-label">Venue Name <span class="text-danger">*</span></label>
            <input type="text" id="venue_name" name="venue_name" class="form-control"
                   value="<?= htmlspecialchars((string) ($_POST['venue_name'] ?? '')) ?>" required>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" id="address" name="address" class="form-control"
                   value="<?= htmlspecialchars((string) ($_POST['address'] ?? '')) ?>">
        </div>

        <div class="mb-3">
            <label for="venue_type" class="form-label">Type</label>
            <input type="text" id="venue_type" name="venue_type" class="form-control"
                   value="<?= htmlspecialchars((string) ($_POST['venue_type'] ?? '')) ?>">
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Create Venue</button>
            <a href="/cms/eventManagement/venues?event=<?= rawurlencode($eventSlug) ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
