<?php
$selectedEvent = $selectedEvent ?? \App\Models\Enums\Event::Tour;
$eventSlug = strtolower($selectedEvent->label());
?>
<div class="container py-4">
    <h1 class="h3 mb-3">Edit Ticket Slot #<?= (int) ($ticket['id'] ?? 0) ?></h1>
    <div class="mb-3">
        <a href="/cms/eventManagement/tickets?event=<?= rawurlencode($eventSlug) ?>" class="btn btn-sm btn-outline-secondary">Back to Tickets</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars((string) $error) ?></div>
    <?php endif; ?>

    <form action="/cms/eventManagement/tickets/edit?event=<?= rawurlencode($eventSlug) ?>" method="POST" class="card p-3">
        <input type="hidden" name="ticket_id" value="<?= (int) ($ticket['id'] ?? 0) ?>">

        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" id="date" name="date" class="form-control"
                   value="<?= htmlspecialchars((string) ($ticket['date'] ?? '')) ?>" required>
        </div>

        <div class="mb-3">
            <label for="start_time" class="form-label">Time</label>
            <input type="time" id="start_time" name="start_time" class="form-control"
                   value="<?= htmlspecialchars((string) ($ticket['start_time'] ?? '')) ?>" required>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Aantal te verkopen</label>
            <input type="number" id="quantity" name="quantity" class="form-control" min="1"
                   value="<?= htmlspecialchars((string) ($ticket['available_spots'] ?? 0)) ?>" required>
            <div class="form-text">Reeds verkocht: <?= (int) ($ticket['amount_sold'] ?? 0) ?></div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="/cms/eventManagement/tickets?event=<?= rawurlencode($eventSlug) ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
