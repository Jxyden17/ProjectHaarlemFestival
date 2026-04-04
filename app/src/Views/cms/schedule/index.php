<?php $sessionRows = (!empty($schedules) && !empty($schedules->sessions)) ? $schedules->sessions : []; ?>
<div class="container-lg py-4 py-md-5">
    <div class="vstack gap-3">
        <section class="cms-page-hero">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <p class="cms-page-hero__eyebrow">Event operations</p>
                    <h1 class="cms-page-hero__title">Schedule Management</h1>
                    <p class="cms-page-hero__description">Manage ticketed sessions, time slots, capacity, and sold tickets for <?= htmlspecialchars($selectedEvent->label()) ?>.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/cms/eventManagement" class="btn btn-outline-secondary">Back to Events</a>
                    <a href="/cms/eventManagement/schedules/create?event_id=<?= $selectedEvent->value ?>" class="btn btn-primary">Add Schedule</a>
                </div>
            </div>
        </section>

        <section class="card border-0 shadow-sm">
            <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <form method="GET" action="/cms/eventManagement/schedules" class="row g-2 align-items-end m-0">
                    <div class="col-12 col-md-auto">
                        <label for="event-switch" class="form-label mb-1">Select Event</label>
                        <select id="event-switch" name="event_id" class="form-select">
                            <?php foreach ($eventTypes as $event): ?>
                                <option value="<?= htmlspecialchars($event->value) ?>"<?= $event->value === $selectedEvent->value ? ' selected' : '' ?>>
                                    <?= htmlspecialchars($event->label()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-outline-secondary">Switch</button>
                    </div>
                </form>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2"><?= count($sessionRows) ?> sessions</span>
                    <a href="/cms/eventManagement/artists?event_id=<?= $selectedEvent->value ?>" class="btn btn-outline-secondary">Artists</a>
                    <a href="/cms/eventManagement/venues?event_id=<?= $selectedEvent->value ?>" class="btn btn-outline-secondary">Venues</a>
                </div>
            </div>
        </section>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Event</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Aantal Tickets</th>
                                <th>Verkocht Tickets</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($sessionRows === []): ?>
                                <tr>
                                    <td colspan="7" class="cms-empty-state">No ticket moments found for this event.</td>
                                </tr>
                            <?php else: ?>
                                <?php $counter = 1; ?>
                                <?php foreach ($sessionRows as $row): ?>
                                    <?php $rowNumber = $counter++; ?>
                                    <tr>
                                        <input type="hidden" name="id" value="<?= $row->id ?>">
                                        <td><?= $rowNumber ?></td>
                                        <td><?= htmlspecialchars($row->event->name) ?></td>
                                        <td><?= htmlspecialchars($row->date ?? 'Unknown') ?></td>
                                        <td><?= htmlspecialchars($row->startTime ?? 'Unknown') ?></td>
                                        <td><?= ($row->availableSpots ?? 0) ?></td>
                                        <td><?= ($row->amountSold ?? 0) ?></td>
                                        <td>
                                            <div class="cms-table-actions">
                                                <a href="/cms/eventManagement/schedules/edit?event_id=<?= $selectedEvent->value ?>&id=<?= $row->id ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <a href="/cms/eventManagement/schedules/view?event_id=<?= $selectedEvent->value ?>&id=<?= $row->id ?>" class="btn btn-sm btn-outline-primary">View Tickets</a>
                                                <a href="/cms/eventManagement/schedules/delete?event_id=<?= $selectedEvent->value ?>&id=<?= $row->id ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete ticket slot #<?= $rowNumber ?>?');">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
