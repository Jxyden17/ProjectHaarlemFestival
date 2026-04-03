<div class="container-lg py-4 py-md-5">
    <div class="vstack gap-3">
        <section class="cms-page-hero">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <p class="cms-page-hero__eyebrow">Event operations</p>
                    <h1 class="cms-page-hero__title">Create Artist</h1>
                    <p class="cms-page-hero__description">Add a new performer record and link it to the correct event context.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/cms/eventManagement/artists?event_id=<?= $selectedEvent->value ?>" class="btn btn-outline-secondary">Back to Artists</a>
                </div>
            </div>
        </section>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="/cms/eventManagement/artists/create?event_id=<?= $selectedEvent->value ?>" method="POST" class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="performer_name" class="form-label">Name</label>
                        <input type="text" id="performer_name" name="performer_name" class="form-control" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="performer_type" class="form-label">Profession</label>
                        <input type="text" id="performer_type" name="performer_type" class="form-control" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="event_id" class="form-label">Select Event</label>
                        <select id="event_id" name="event_id" class="form-select" required>
                            <?php foreach ($eventTypes as $event): ?>
                                <option value="<?= $event->value ?>"<?= $selectedEvent->value === $event->value ? ' selected' : '' ?>>
                                    <?= htmlspecialchars($event->label()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" id="description" name="description" class="form-control">
                    </div>

                    <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                        <button type="submit" class="btn btn-primary">Create Artist</button>
                        <a href="/cms/eventManagement/artists?event_id=<?= $selectedEvent->value ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
