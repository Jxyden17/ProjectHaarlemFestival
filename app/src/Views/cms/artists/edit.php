<div class="container-lg py-4 py-md-5">
    <div class="vstack gap-3">
        <section class="cms-page-hero">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <p class="cms-page-hero__eyebrow">Event operations</p>
                    <h1 class="cms-page-hero__title">Edit Artist</h1>
                    <p class="cms-page-hero__description">Update the artist record for <?= htmlspecialchars($artiste->performerName) ?> and keep its event linkage accurate.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/cms/eventManagement/artists?event_id=<?= $selectedEvent->value ?>" class="btn btn-outline-secondary">Back to Artists</a>
                </div>
            </div>
        </section>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="/cms/eventManagement/artists/edit?event_id=<?= $selectedEvent->value ?>" method="POST" class="row g-3">
                    <input type="hidden" name="id" value="<?= (int)$artiste->id ?>">

                    <div class="col-12 col-md-6">
                        <label for="performer_name" class="form-label">Name</label>
                        <input type="text" id="performer_name" name="performer_name" class="form-control" value="<?= htmlspecialchars($artiste->performerName) ?>" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="performer_type" class="form-label">Profession</label>
                        <input type="text" id="performer_type" name="performer_type" class="form-control" value="<?= $artiste->performerType ?>" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="event_id" class="form-label">Event</label>
                        <select id="event_id" name="event_id" class="form-select">
                            <?php foreach ($eventTypes as $event): ?>
                                <option value="<?= $event->value ?>"<?= $artiste->eventId === $event->value ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($event->label()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" id="description" name="description" class="form-control" value="<?= $artiste->description ?>" required>
                    </div>

                    <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="/cms/eventManagement/artists?event_id=<?= $selectedEvent->value ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
