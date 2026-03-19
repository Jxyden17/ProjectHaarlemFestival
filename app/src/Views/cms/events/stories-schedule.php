<?php
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorPerformerRowViewModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorSessionRowViewModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorVenueRowViewModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorViewModel;

$editorViewModel = (isset($editorViewModel) && $editorViewModel instanceof ScheduleEditorViewModel)
    ? $editorViewModel
    : new ScheduleEditorViewModel('', [], [], []);
$sessions = $editorViewModel->sessions;
$venues = $editorViewModel->venues;
$performers = $editorViewModel->performers;
$formAction = (string)($formAction ?? '/cms/events/stories/schedule');
?>

<div class="container-lg py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-1"><?= htmlspecialchars((string)($title ?? 'Stories Schedule')) ?></h1>
            <p class="text-muted mb-0">Manage the live schedule data for Stories with a section-based editor.</p>
        </div>
        <a href="/cms/events" class="btn btn-outline-secondary">Back to Events</a>
    </div>

    <?php
    $successMessage = 'Stories schedule updated.';
    include __DIR__ . '/../../partialsViews/cms/form-feedback.php';
    ?>

    <form method="POST" action="<?= htmlspecialchars($formAction) ?>" class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="mb-4">
                <h2 class="h5 mb-2">Overview</h2>
            </div>

            <hr>

            <h2 class="h5 mb-3">Venues</h2>
            <div class="row g-3 mb-4">
                <?php foreach ($venues as $index => $venue): ?>
                    <?php if (!$venue instanceof ScheduleEditorVenueRowViewModel) { continue; } ?>
                    <div class="col-12 col-xl-6">
                        <div class="border rounded p-3 h-100">
                            <div class="small text-muted mb-2">Venue #<?= (int)$venue->id ?></div>
                            <input type="hidden" name="venues[<?= (int)$index ?>][id]" value="<?= $venue->id ?>">
                            <div class="mb-2">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="venues[<?= (int)$index ?>][name]" value="<?= htmlspecialchars($venue->name) ?>" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" name="venues[<?= (int)$index ?>][address]" value="<?= htmlspecialchars($venue->address) ?>">
                            </div>
                            <div>
                                <label class="form-label">Type</label>
                                <input type="text" class="form-control" name="venues[<?= (int)$index ?>][type]" value="<?= htmlspecialchars($venue->type) ?>">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <hr>

            <h2 class="h5 mb-3">Performers</h2>
            <div class="row g-3 mb-4">
                <?php foreach ($performers as $index => $performer): ?>
                    <?php if (!$performer instanceof ScheduleEditorPerformerRowViewModel) { continue; } ?>
                    <div class="col-12">
                        <div class="border rounded p-3">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <div class="small text-muted">Performer #<?= (int)$performer->id ?></div>
                                    <strong><?= htmlspecialchars($performer->name) ?></strong>
                                </div>
                            </div>
                            <input type="hidden" name="performers[<?= (int)$index ?>][id]" value="<?= $performer->id ?>">
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" name="performers[<?= (int)$index ?>][name]" value="<?= htmlspecialchars($performer->name) ?>" required>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Type</label>
                                    <input type="text" class="form-control" name="performers[<?= (int)$index ?>][type]" value="<?= htmlspecialchars($performer->type) ?>">
                                </div>
                                <div class="col-12 col-md-5">
                                    <label class="form-label">Description</label>
                                    <input type="text" class="form-control" name="performers[<?= (int)$index ?>][description]" value="<?= htmlspecialchars($performer->description) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <hr>

            <h2 class="h5 mb-3">Sessions</h2>
            <div class="d-grid gap-3">
                <?php foreach ($sessions as $index => $session): ?>
                    <?php if (!$session instanceof ScheduleEditorSessionRowViewModel) { continue; } ?>
                    <div class="border rounded p-3">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <div class="small text-muted">Session #<?= (int)$session->id ?></div>
                                <strong><?= htmlspecialchars($session->label !== '' ? $session->label : 'Untitled session') ?></strong>
                            </div>
                            <span class="badge text-bg-light">Sold: <?= (int)$session->amountSold ?></span>
                        </div>

                        <input type="hidden" name="sessions[<?= (int)$index ?>][id]" value="<?= (int)$session->id ?>">
                        <input type="hidden" name="sessions[<?= (int)$index ?>][amount_sold]" value="<?= (int)$session->amountSold ?>">

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-3">
                                <label class="form-label">Date</label>
                                <input type="date" class="form-control" name="sessions[<?= (int)$index ?>][date]" value="<?= htmlspecialchars($session->date) ?>" required>
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="form-label">Time</label>
                                <input type="time" class="form-control" name="sessions[<?= (int)$index ?>][start_time]" value="<?= htmlspecialchars($session->startTime) ?>" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Venue</label>
                                <select class="form-select" name="sessions[<?= (int)$index ?>][venue_id]" required>
                                    <option value="">Select venue</option>
                                    <?php foreach ($venues as $venue): ?>
                                        <?php if (!$venue instanceof ScheduleEditorVenueRowViewModel) { continue; } ?>
                                        <option value="<?= (int)$venue->id ?>" <?= (int)$venue->id === (int)$session->venueId ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($venue->name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label">Label</label>
                                <input type="text" class="form-control" name="sessions[<?= (int)$index ?>][label]" value="<?= htmlspecialchars($session->label) ?>">
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label">Price</label>
                                <input type="number" min="0" step="0.01" class="form-control" name="sessions[<?= (int)$index ?>][price]" value="<?= htmlspecialchars($session->price) ?>" required>
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label">Available Spots</label>
                                <input type="number" min="0" step="1" class="form-control" name="sessions[<?= (int)$index ?>][available_spots]" value="<?= htmlspecialchars((string)$session->availableSpots) ?>" required>
                            </div>
                        </div>

                        <label class="form-label">Performers in this session</label>
                        <div class="row g-2">
                            <?php $selectedPerformerIds = array_map('intval', $session->performerIds); ?>
                            <?php foreach ($performers as $performer): ?>
                                <?php if (!$performer instanceof ScheduleEditorPerformerRowViewModel) { continue; } ?>
                                <?php $performerId = (int)$performer->id; ?>
                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="form-check border rounded px-3 py-2 h-100">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="sessions[<?= (int)$index ?>][performer_ids][]"
                                            id="stories-session-<?= (int)$index ?>-performer-<?= $performerId ?>"
                                            value="<?= $performerId ?>"
                                            <?= in_array($performerId, $selectedPerformerIds, true) ? 'checked' : '' ?>
                                        >
                                        <label class="form-check-label w-100" for="stories-session-<?= (int)$index ?>-performer-<?= $performerId ?>">
                                            <?= htmlspecialchars($performer->name) ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Save Schedule</button>
        </div>
    </form>
</div>
