<div class="container-lg py-4 py-md-5">
    <div class="vstack gap-3">
        <section class="cms-page-hero">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <p class="cms-page-hero__eyebrow">Event operations</p>
                    <h1 class="cms-page-hero__title">Edit Venue</h1>
                    <p class="cms-page-hero__description">Update venue details for <?= htmlspecialchars($venue->venueName) ?> and keep the event mapping correct.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/cms/eventManagement/venues?event_id=<?= $selectedEvent->value ?>" class="btn btn-outline-secondary">Back to Venues</a>
                </div>
            </div>
        </section>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="/cms/eventManagement/venues/edit?event_id=<?= $selectedEvent->value ?>" method="POST" class="row g-3">
                    <input type="hidden" name="id" value="<?= $venue->id ?>">

                    <div class="col-12">
                        <label for="venue_name" class="form-label">Venue Name <span class="text-danger">*</span></label>
                        <input type="text" id="venue_name" name="venue_name" class="form-control"
                               value="<?= htmlspecialchars($venue->venueName) ?>" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" id="address" name="address" class="form-control"
                               value="<?= htmlspecialchars($venue->address ?? '') ?>">
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="venue_type" class="form-label">Type</label>
                        <input type="text" id="venue_type" name="venue_type" class="form-control"
                               value="<?= htmlspecialchars($venue->venueType ?? '') ?>">
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="event_id" class="form-label">Event</label>
                        <select id="event_id" name="event_id" class="form-select">
                            <?php foreach ($eventTypes as $event): ?>
                                <option value="<?= $event->value ?>"<?= $venue->eventId === $event->value ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($event->label()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="/cms/eventManagement/venues?event_id=<?= $selectedEvent->value ?>&id=<?= $venue->id ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
