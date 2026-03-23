<?php
$selectedEvent = $selectedEvent ?? \App\Models\Enums\Event::Tour;
$eventSlug = strtolower($selectedEvent->label());
$eventTypes = \App\Models\Enums\Event::cases();
?>
<div class="container py-4">
    <h1 class="h3 mb-3">Create Tickets - <?= htmlspecialchars($selectedEvent->label()) ?></h1>
    <form method="GET" action="/cms/eventManagement/tickets/create" class="row g-2 align-items-end mb-3">
        <div class="col-sm-6 col-md-4">
            <label for="event-switch" class="form-label">Select Event</label>
            <select id="event-switch" name="event" class="form-select">
                <?php foreach ($eventTypes as $event): ?>
                    <?php $slug = strtolower($event->label()); ?>
                    <option value="<?= htmlspecialchars($slug) ?>"<?= $slug === $eventSlug ? ' selected' : '' ?>>
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
        <a href="/cms/eventManagement/tickets?event=<?= rawurlencode($eventSlug) ?>" class="btn btn-sm btn-outline-secondary">Back to Tickets</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars((string) $error) ?></div>
    <?php endif; ?>

    <form action="/cms/eventManagement/tickets/create?event=<?= rawurlencode($eventSlug) ?>" method="POST" class="card p-3">
        <div class="mb-3">
            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
            <input type="date" id="date" name="date" class="form-control"
                   value="<?= htmlspecialchars((string) ($_POST['date'] ?? '')) ?>" required>
        </div>

        <div class="mb-3">
            <label for="start_time" class="form-label">Time <span class="text-danger">*</span></label>
            <input type="time" id="start_time" name="start_time" class="form-control"
                   value="<?= htmlspecialchars((string) ($_POST['start_time'] ?? '')) ?>" required>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Aantal te verkopen <span class="text-danger">*</span></label>
            <input type="number" id="quantity" name="quantity" class="form-control" min="1" max="200"
                   value="<?= htmlspecialchars((string) ($_POST['quantity'] ?? '1')) ?>" required>
        </div>

        <div class="alert alert-secondary mb-3">
            Dit maakt een ticketmoment aan in sessions voor dit event. Koppeling aan gebruikers/orders komt later.
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Create Ticket Slot</button>
            <a href="/cms/eventManagement/tickets?event=<?= rawurlencode($eventSlug) ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
