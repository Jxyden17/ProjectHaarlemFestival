<?php
$session = $schedule->sessions[0] ?? null;
$selectedPerformerIds = $session !== null ? array_map('intval', $session->performerIds) : [];
?>
<div class="container-lg py-4 py-md-5">
    <div class="vstack gap-3">
        <section class="cms-page-hero">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <p class="cms-page-hero__eyebrow">Event operations</p>
                    <h1 class="cms-page-hero__title">Edit Schedule</h1>
                    <p class="cms-page-hero__description">Update session details, venue assignment, performers, and ticket settings for <?= htmlspecialchars($selectedEvent->label()) ?>.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/cms/eventManagement/schedules?event_id=<?= $selectedEvent->value ?>" class="btn btn-outline-secondary">Back to Schedules</a>
                </div>
            </div>
        </section>

        <?php if ($session === null): ?>
            <div class="alert alert-danger">Session not found.</div>
        <?php else: ?>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="/cms/eventManagement/schedules/edit?event_id=<?= $selectedEvent->value ?>" method="POST" class="row g-3">
                        <input type="hidden" name="id" value="<?= (int)$session->id ?>">
                        <input type="hidden" name="event_id" value="<?= (int)$selectedEvent->value ?>">

                        <div class="col-12 col-md-6">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" id="date" name="date" class="form-control"
                                   value="<?= htmlspecialchars($session->date) ?>" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="start_time" class="form-label">Time</label>
                            <input type="time" id="start_time" name="start_time" class="form-control"
                                   value="<?= htmlspecialchars($session->startTime) ?>" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="venue_id" class="form-label">Venue</label>
                            <select id="venue_id" name="venue_id" class="form-select" required>
                                <?php foreach (($schedule->venues ?? []) as $venue): ?>
                                    <option value="<?= (int)$venue->id ?>"<?= $venue->id === $session->venueId ? ' selected' : '' ?>>
                                        <?= htmlspecialchars((string)($venue->name ?? '')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="label" class="form-label">Age</label>
                            <input type="text" id="label" name="label" class="form-control"
                                value="<?= htmlspecialchars($session->label ?? '') ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Performers</label>
                            <div class="border rounded p-3">
                                <?php foreach (($performers ?? []) as $performer): ?>
                                    <?php $performerId = (int)$performer->id; ?>
                                    <div class="form-check mb-2">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="performer_ids[]"
                                            value="<?= $performerId ?>"
                                            id="performer_<?= $performerId ?>"
                                            <?= in_array($performerId, $selectedPerformerIds, true) ? 'checked' : '' ?>
                                        >
                                        <label class="form-check-label" for="performer_<?= $performerId ?>">
                                            <?= htmlspecialchars($performer->performerName) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="form-text">
                                Vink de artiesten aan die je wilt koppelen. Geen vinkje = geen artiesten.
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" id="price" name="price" class="form-control" min="0" step="0.01"
                                value="<?= htmlspecialchars($session->price) ?>" required>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="language_id" class="form-label">Language</label>
                            <select id="language_id" name="language_id" class="form-select">
                                <?php foreach ($language as $lang): ?>
                                    <option value="<?= $lang->value ?>"<?= (($session->language?->value ?? null) === $lang->value) ? ' selected' : '' ?>>
                                        <?= htmlspecialchars($lang->label()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="available_spots" class="form-label">Aantal tickets</label>
                            <input type="number" id="available_spots" name="available_spots" class="form-control" min="-1" max="5000"
                                   value="<?= $session->availableSpots ?>" required>
                            <div class="form-text">
                                Verkocht Tickets: <?= $session->amountSold ?>.
                                Gebruik `-1` voor unlimited.
                            </div>
                        </div>

                        <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="/cms/eventManagement/schedules?event_id=<?= $selectedEvent->value ?>" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
