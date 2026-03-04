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
$formAction = (string)($formAction ?? '/cms/events/dance-schedule');
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0"><?= htmlspecialchars((string)($title ?? 'Event Schedule')) ?></h1>
        <a href="/cms/events" class="btn btn-outline-secondary">Back to Events</a>
    </div>

    <?php
    $successMessage = 'Dance schedule updated.';
    include __DIR__ . '/../../partialsViews/cms/form-feedback.php';
    ?>

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
                            <?php if (!$venue instanceof ScheduleEditorVenueRowViewModel) { continue; } ?>
                            <tr>
                                <td>
                                    <?= $venue->id ?>
                                    <input type="hidden" name="venues[<?= (int)$index ?>][id]" value="<?= $venue->id ?>">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="venues[<?= (int)$index ?>][name]" value="<?= htmlspecialchars($venue->name) ?>" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="venues[<?= (int)$index ?>][address]" value="<?= htmlspecialchars($venue->address) ?>">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="venues[<?= (int)$index ?>][type]" value="<?= htmlspecialchars($venue->type) ?>">
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
                            <th>Artist Image</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($performers as $index => $performer): ?>
                            <?php if (!$performer instanceof ScheduleEditorPerformerRowViewModel) { continue; } ?>
                            <tr>
                                <td>
                                    <?= $performer->id ?>
                                    <input type="hidden" name="performers[<?= (int)$index ?>][id]" value="<?= $performer->id ?>">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="performers[<?= (int)$index ?>][name]" value="<?= htmlspecialchars($performer->name) ?>" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="performers[<?= (int)$index ?>][type]" value="<?= htmlspecialchars($performer->type) ?>">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="performers[<?= (int)$index ?>][description]" value="<?= htmlspecialchars($performer->description) ?>">
                                </td>
                                <td style="min-width: 300px;">
                                    <input type="hidden" class="performer-artist-item-id" value="<?= $performer->artistSectionItemId ?>">
                                    <input type="hidden" class="performer-artist-image" value="<?= htmlspecialchars($performer->artistImagePath) ?>">
                                    <div class="d-flex flex-wrap gap-2 align-items-center performer-image-row">
                                        <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                                        <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                                        <a
                                            href="<?= htmlspecialchars($performer->artistImagePath) ?>"
                                            class="btn btn-sm btn-outline-secondary performer-download-link<?= $performer->artistImagePath === '' ? ' d-none' : '' ?>"
                                            download
                                        >
                                            Download
                                        </a>
                                    </div>
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
                            if (!$session instanceof ScheduleEditorSessionRowViewModel) {
                                continue;
                            }

                            $id = $session->id;
                            $selectedVenueId = $session->venueId;
                            ?>
                            <tr>
                                <td>
                                    <?= $id ?>
                                    <input type="hidden" name="sessions[<?= (int)$index ?>][id]" value="<?= $id ?>">
                                    <input type="hidden" name="sessions[<?= (int)$index ?>][amount_sold]" value="<?= $session->amountSold ?>">
                                </td>
                                <td>
                                    <input type="date" class="form-control form-control-sm" name="sessions[<?= (int)$index ?>][date]" value="<?= htmlspecialchars($session->date) ?>" required>
                                </td>
                                <td>
                                    <input type="time" class="form-control form-control-sm" name="sessions[<?= (int)$index ?>][start_time]" value="<?= htmlspecialchars($session->startTime) ?>" required>
                                </td>
                                <td>
                                    <select class="form-select form-select-sm" name="sessions[<?= (int)$index ?>][venue_id]" required>
                                        <option value="">Select venue</option>
                                        <?php foreach ($venues as $venue): ?>
                                            <?php
                                            if (!$venue instanceof ScheduleEditorVenueRowViewModel) {
                                                continue;
                                            }

                                            $venueId = $venue->id;
                                            ?>
                                            <option value="<?= $venueId ?>" <?= $venueId === $selectedVenueId ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($venue->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="sessions[<?= (int)$index ?>][label]" value="<?= htmlspecialchars($session->label) ?>">
                                </td>
                                <td>
                                    <input type="number" min="0" step="0.01" class="form-control form-control-sm" name="sessions[<?= (int)$index ?>][price]" value="<?= htmlspecialchars($session->price) ?>" required>
                                </td>
                                <td>
                                    <input type="number" min="0" step="1" class="form-control form-control-sm" name="sessions[<?= (int)$index ?>][available_spots]" value="<?= htmlspecialchars((string)$session->availableSpots) ?>" required>
                                </td>
                                <td><?= $session->amountSold ?></td>
                                <td style="min-width: 280px;">
                                    <?php $selectedPerformerIds = $session->performerIds; ?>
                                    <div class="border rounded p-2" style="max-height: 140px; overflow-y: auto;">
                                        <?php foreach ($performers as $performer): ?>
                                            <?php
                                            if (!$performer instanceof ScheduleEditorPerformerRowViewModel) {
                                                continue;
                                            }

                                            $performerId = $performer->id;
                                            $isSelected = in_array($performerId, array_map('intval', $selectedPerformerIds), true);
                                            ?>
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    name="sessions[<?= (int)$index ?>][performer_ids][]"
                                                    id="session-<?= (int)$index ?>-performer-<?= (int)$performerId ?>"
                                                    value="<?= (int)$performerId ?>"
                                                    <?= $isSelected ? 'checked' : '' ?>
                                                >
                                                <label class="form-check-label" for="session-<?= (int)$index ?>-performer-<?= (int)$performerId ?>">
                                                    <?= htmlspecialchars($performer->name) ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
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

<?php $danceScheduleJsVersion = @filemtime(__DIR__ . '/../../../../public/js/cms/dance-schedule.js') ?: time(); ?>
<script src="/js/cms/dance-schedule.js?v=<?= (int)$danceScheduleJsVersion ?>"></script>
