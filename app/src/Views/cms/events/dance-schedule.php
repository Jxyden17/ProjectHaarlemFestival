<?php
$editorData = is_array($editorData ?? null) ? $editorData : [];
$sessions = is_array($editorData['sessions'] ?? null) ? $editorData['sessions'] : [];
$venues = is_array($editorData['venues'] ?? null) ? $editorData['venues'] : [];
$performers = is_array($editorData['performers'] ?? null) ? $editorData['performers'] : [];
$formAction = (string)($formAction ?? '/cms/events/dance-schedule');
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0"><?= htmlspecialchars((string)($title ?? 'Event Schedule')) ?></h1>
        <a href="/cms/events" class="btn btn-outline-secondary">Back to Events</a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success" role="alert">
            Dance schedule updated.
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars((string)$error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= htmlspecialchars($formAction) ?>" class="card">
        <div class="card-body">
            <p class="text-muted mb-3">
                Edit all schedule-related data for Dance: venues, performers, sessions, and performer lineups per session.
            </p>

            <h2 class="h5">Venues</h2>
            <div class="table-responsive mb-4">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($venues as $index => $venue): ?>
                            <tr>
                                <td>
                                    <?= (int)($venue['id'] ?? 0) ?>
                                    <input type="hidden" name="venues[<?= (int)$index ?>][id]" value="<?= (int)($venue['id'] ?? 0) ?>">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="venues[<?= (int)$index ?>][name]" value="<?= htmlspecialchars((string)($venue['name'] ?? '')) ?>" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="venues[<?= (int)$index ?>][address]" value="<?= htmlspecialchars((string)($venue['address'] ?? '')) ?>">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="venues[<?= (int)$index ?>][type]" value="<?= htmlspecialchars((string)($venue['type'] ?? '')) ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <h2 class="h5">Performers</h2>
            <div class="table-responsive mb-4">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($performers as $index => $performer): ?>
                            <tr>
                                <td>
                                    <?= (int)($performer['id'] ?? 0) ?>
                                    <input type="hidden" name="performers[<?= (int)$index ?>][id]" value="<?= (int)($performer['id'] ?? 0) ?>">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="performers[<?= (int)$index ?>][name]" value="<?= htmlspecialchars((string)($performer['name'] ?? '')) ?>" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="performers[<?= (int)$index ?>][type]" value="<?= htmlspecialchars((string)($performer['type'] ?? '')) ?>">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="performers[<?= (int)$index ?>][description]" value="<?= htmlspecialchars((string)($performer['description'] ?? '')) ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <h2 class="h5">Sessions</h2>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Venue</th>
                            <th>Label</th>
                            <th>Price</th>
                            <th>Available Spots</th>
                            <th>Sold</th>
                            <th>Performers</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sessions as $index => $session): ?>
                            <?php
                            $id = (int)($session['id'] ?? 0);
                            $selectedVenueId = (int)($session['venue_id'] ?? 0);
                            ?>
                            <tr>
                                <td>
                                    <?= $id ?>
                                    <input type="hidden" name="sessions[<?= (int)$index ?>][id]" value="<?= $id ?>">
                                    <input type="hidden" name="sessions[<?= (int)$index ?>][amount_sold]" value="<?= (int)($session['amount_sold'] ?? 0) ?>">
                                </td>
                                <td>
                                    <input type="date" class="form-control form-control-sm" name="sessions[<?= (int)$index ?>][date]" value="<?= htmlspecialchars((string)($session['date'] ?? '')) ?>" required>
                                </td>
                                <td>
                                    <input type="time" class="form-control form-control-sm" name="sessions[<?= (int)$index ?>][start_time]" value="<?= htmlspecialchars((string)($session['start_time'] ?? '')) ?>" required>
                                </td>
                                <td>
                                    <select class="form-select form-select-sm" name="sessions[<?= (int)$index ?>][venue_id]" required>
                                        <option value="">Select venue</option>
                                        <?php foreach ($venues as $venue): ?>
                                            <?php $venueId = (int)($venue['id'] ?? 0); ?>
                                            <option value="<?= $venueId ?>" <?= $venueId === $selectedVenueId ? 'selected' : '' ?>>
                                                <?= htmlspecialchars((string)($venue['name'] ?? '')) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="sessions[<?= (int)$index ?>][label]" value="<?= htmlspecialchars((string)($session['label'] ?? '')) ?>">
                                </td>
                                <td>
                                    <input type="number" min="0" step="0.01" class="form-control form-control-sm" name="sessions[<?= (int)$index ?>][price]" value="<?= htmlspecialchars((string)($session['price'] ?? '0.00')) ?>" required>
                                </td>
                                <td>
                                    <input type="number" min="0" step="1" class="form-control form-control-sm" name="sessions[<?= (int)$index ?>][available_spots]" value="<?= htmlspecialchars((string)($session['available_spots'] ?? '0')) ?>" required>
                                </td>
                                <td><?= (int)($session['amount_sold'] ?? 0) ?></td>
                                <td style="min-width: 240px;">
                                    <?php $selectedPerformerIds = is_array($session['performer_ids'] ?? null) ? $session['performer_ids'] : []; ?>
                                    <select
                                        class="form-select form-select-sm"
                                        name="sessions[<?= (int)$index ?>][performer_ids][]"
                                        multiple
                                        size="4"
                                    >
                                        <?php foreach ($performers as $performer): ?>
                                            <?php $performerId = (int)($performer['id'] ?? 0); ?>
                                            <option value="<?= $performerId ?>" <?= in_array($performerId, array_map('intval', $selectedPerformerIds), true) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars((string)($performer['name'] ?? '')) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Save Schedule</button>
        </div>
    </form>
</div>
