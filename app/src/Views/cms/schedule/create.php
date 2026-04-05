<div class="container-lg py-4 py-md-5">
    <div class="vstack gap-3">
        <section class="cms-page-hero">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <p class="cms-page-hero__eyebrow">Event operations</p>
                    <h1 class="cms-page-hero__title">Create Schedule</h1>
                    <p class="cms-page-hero__description">Add a new session for <?= htmlspecialchars($selectedEvent->label()) ?> with its timing, venue, linked performers, and ticket data.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/cms/eventManagement/schedules?event_id=<?= $selectedEvent->value ?>" class="btn btn-outline-secondary">Back to Schedules</a>
                </div>
            </div>
        </section>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="/cms/eventManagement/schedules/create?event_id=<?= $selectedEvent->value ?>" method="POST" class="row g-3">
                    <input type="hidden" name="event_id" value="<?= (int)$selectedEvent->value ?>">

                    <div class="col-12 col-md-6">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" id="date" name="date" class="form-control" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="start_time" class="form-label">Time</label>
                        <input type="time" id="start_time" name="start_time" class="form-control" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="venue_id" class="form-label">Venue</label>
                        <select id="venue_id" name="venue_id" class="form-select" required>
                            <?php foreach ($venues as $venue): ?>
                                <option value="<?= (int)$venue->id ?>">
                                    <?= htmlspecialchars($venue->venueName) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="label" class="form-label">Age</label>
                        <input type="text" id="label" name="label" class="form-control">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Performers</label>
                        <div class="border rounded p-3">
                            <?php foreach ($performers as $performer): ?>
                                <div class="form-check mb-2">
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

                    <div class="col-12 col-md-4">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" id="price" name="price" class="form-control" min="0" step="0.5" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="language_id" class="form-label">Language</label>
                        <select id="language_id" name="language_id" class="form-select">
                            <?php foreach ($language as $lang): ?>
                                <option value="<?= $lang->value ?>">
                                    <?= htmlspecialchars($lang->label()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="available_spots" class="form-label">Aantal tickets</label>
                        <input type="number" id="available_spots" name="available_spots" class="form-control" min="-1" max="5000" required>
                        <div class="form-text">
                            Gebruik `-1` voor unlimited.
                        </div>
                    </div>

                    <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                        <button type="submit" class="btn btn-primary">Create Schedule</button>
                        <a href="/cms/eventManagement/schedules?event_id=<?= $selectedEvent->value ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
