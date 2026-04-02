<div class="container py-4">
    <h1 class="h3 mb-3">
        Create Schedule - <?= htmlspecialchars($selectedEvent->label()) ?>
    </h1>

    <div class="mb-3">
        <a href="/cms/eventManagement/schedules?event_id=<?= $selectedEvent->value ?>" class="btn btn-sm btn-outline-secondary">
            Back to Schedules
        </a>
    </div>
        <form action="/cms/eventManagement/schedules/create?event_id=<?= $selectedEvent->value ?>" method="POST" class="card p-3">
                <input type="hidden" name="event_id" value="<?= (int)$selectedEvent->value ?>">

            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" id="date" name="date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="start_time" class="form-label">Time</label>
                <input type="time" id="start_time" name="start_time" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="venue_id" class="form-label">Venue</label>
                <select id="venue_id" name="venue_id" class="form-select" required>
                    <?php foreach ($venues as $venue): ?>
                        <option value="<?= (int)$venue->id ?>">
                            <?= htmlspecialchars($venue->venueName) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Performers</label>
                <div>
                    <?php foreach ($performers as $performer): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="performer_ids[]" value="<?= (int)$performer->id ?>" id="performer_<?= (int)$performer->id ?>">
                            <label class="form-check-label" for="performer_<?= (int)$performer->id ?>">
                                <?= htmlspecialchars($performer->performerName) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="form-text">
                    Vink de artiesten aan die je wilt koppelen. Geen vinkje = geen artiesten.
                </div>
            </div>

            <div class="mb-3">
                <label for="label" class="form-label">Age</label>
                <input type="text" id="label" name="label" class="form-control">
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" id="price" name="price" class="form-control" min="0" step="0.5" required>
            </div>

            <div class="mb-3">
                <label for="language_id" class="form-label">Language</label>
                <select id="language_id" name="language_id" class="form-select">
                    <?php foreach ($language as $lang): ?>
                        <option value="<?= $lang->value ?>">
                            <?= htmlspecialchars($lang->label()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="available_spots" class="form-label">Aantal tickets</label>
                <input type="number" id="available_spots" name="available_spots" class="form-control" min="-1" max="5000" required>
                <div class="form-text">
                    Gebruik `-1` voor unlimited.
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Create Schedule</button>
                <a href="/cms/eventManagement/schedules?event_id=<?= $selectedEvent->value ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
</div>