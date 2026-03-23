<?php
$session = $schedule->sessions[0] ?? null;
?>
<div class="container py-4">
    <h1 class="h3 mb-3">
        Edit Schedule - <?= htmlspecialchars($selectedEvent->label()) ?>
    </h1>

    <div class="mb-3">
        <a href="/cms/eventManagement/schedules?event_id=<?= $selectedEvent->value ?>" class="btn btn-sm btn-outline-secondary">
            Back to Schedules
        </a>
    </div>

    <?php if ($session === null): ?>
        <div class="alert alert-danger">Session not found.</div>
    <?php else: ?>
        <form action="/cms/eventManagement/schedules/edit?event_id=<?= $selectedEvent->value ?>" method="POST" class="card p-3">
            <input type="hidden" name="id" value="<?= (int)$session->id ?>">
            <input type="hidden" name="event_id" value="<?= (int)$selectedEvent->value ?>">

            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" id="date" name="date" class="form-control"
                       value="<?= htmlspecialchars($session->date) ?>" required>
            </div>

            <div class="mb-3">
                <label for="start_time" class="form-label">Time</label>
                <input type="time" id="start_time" name="start_time" class="form-control"
                       value="<?= htmlspecialchars($session->startTime) ?>" required>
            </div>

            <div class="mb-3">
                <label for="venue_id" class="form-label">Venue</label>
                <select id="venue_id" name="venue_id" class="form-select" required>
                    <?php foreach (($schedule->venues ?? []) as $venue): ?>
                        <option value="<?= (int)$venue->id ?>"<?= $venue->id === $session->venueId ? ' selected' : '' ?>>
                            <?= htmlspecialchars($venue->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="label" class="form-label">Age</label>
                <input type="text" id="label" name="label" class="form-control"
                    value="<?= htmlspecialchars($session->label ?? '') ?>">
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" id="price" name="price" class="form-control" min="0" step="0.01"
                    value="<?= htmlspecialchars($session->price) ?>" required>
            </div>

            <div class="mb-3">
                <label for="language_id" class="form-label">Language</label>
                <select id="language_id" name="language_id" class="form-select">
                    <?php foreach ($language as $lang): ?>
                        <option value="<?= $lang->value ?>"<?= ($session->languageId === $lang->value) ? ' selected' : '' ?>>
                            <?= htmlspecialchars($lang->label()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="available_spots" class="form-label">Aantal tickets</label>
                <input type="number" id="available_spots" name="available_spots" class="form-control" min="-1" max="5000"
                       value="<?= $session->availableSpots ?>" required>
                <div class="form-text">
                    Verkocht Tickets: <?= $session->amountSold ?>.
                    Gebruik `-1` voor unlimited.
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="/cms/eventManagement/schedules?event_id=<?= $selectedEvent->value ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    <?php endif; ?>
</div>