<div class="container-lg py-4 py-md-5">
    <div class="vstack gap-3">
        <section class="cms-page-hero">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <p class="cms-page-hero__eyebrow">Event operations</p>
                    <h1 class="cms-page-hero__title">Create Venue</h1>
                    <p class="cms-page-hero__description">Add a new venue for <?= htmlspecialchars($selectedEvent->label()) ?> and capture its operational details.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/cms/eventManagement/venues?event_id=<?= $selectedEvent->value ?>" class="btn btn-outline-secondary">Back to Venues</a>
                </div>
            </div>
        </section>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="/cms/eventManagement/venues/create?event_id=<?= $selectedEvent->value ?>" method="POST" class="row g-3">
                    <input type="hidden" name="event_id" value="<?= $selectedEvent->value ?>">
                    <div class="col-12">
                        <label for="venue_name" class="form-label">Venue Name <span class="text-danger">*</span></label>
                        <input type="text" id="venue_name" name="venue_name" class="form-control"
                               value="<?= htmlspecialchars((string) ($_POST['venue_name'] ?? '')) ?>" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" id="address" name="address" class="form-control"
                               value="<?= htmlspecialchars((string) ($_POST['address'] ?? '')) ?>">
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="venue_type" class="form-label">Type</label>
                        <input type="text" id="venue_type" name="venue_type" class="form-control"
                               value="<?= htmlspecialchars((string) ($_POST['venue_type'] ?? '')) ?>">
                    </div>

                    <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                        <button type="submit" class="btn btn-primary">Create Venue</button>
                        <a href="/cms/eventManagement/venues?event_id=<?= $selectedEvent->value?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
