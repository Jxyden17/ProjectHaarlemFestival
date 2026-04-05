<?php
use App\Models\Enums\Event;
$eventid = $selectedEvent->value;
$isDanceEvent = $selectedEvent === Event::Dance;
$danceArtistMediaById = is_array($danceArtistMediaById ?? null) ? $danceArtistMediaById : [];
$artistRows = is_array($artistes ?? null) ? $artistes : [];
?>
<div class="container-lg py-4 py-md-5<?= $isDanceEvent ? ' dance-artist-management' : '' ?>">
    <div class="vstack gap-3">
        <section class="cms-page-hero">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <p class="cms-page-hero__eyebrow">Event operations</p>
                    <h1 class="cms-page-hero__title">Artist Management</h1>
                    <p class="cms-page-hero__description">Maintain artist records for <?= htmlspecialchars($selectedEvent->label()) ?>, including descriptive content and linked media where applicable.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/cms/eventManagement" class="btn btn-outline-secondary">Back to Events</a>
                    <a href="/cms/eventManagement/artists/create?event_id=<?= $eventid ?>" class="btn btn-primary">Add Artist</a>
                </div>
            </div>
        </section>

        <section class="card border-0 shadow-sm">
            <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2"><?= htmlspecialchars($selectedEvent->label()) ?></span>
                <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2"><?= count($artistRows) ?> artists</span>
                <?php if ($isDanceEvent): ?>
                    <span class="badge rounded-pill border border-secondary-subtle bg-transparent text-muted px-3 py-2">Media uploads enabled</span>
                <?php endif; ?>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="/cms/eventManagement/venues?event_id=<?= $eventid ?>" class="btn btn-outline-secondary">Venues</a>
                    <a href="/cms/eventManagement/schedules?event_id=<?= $eventid ?>" class="btn btn-outline-secondary">Schedules</a>
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
                                <th>Name</th>
                                <th>Profession</th>
                                <th>Event</th>
                                <th>Description</th>
                                <?php if ($isDanceEvent): ?>
                                    <th>Artist Image</th>
                                <?php endif; ?>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($artistRows === []): ?>
                                <tr>
                                    <td colspan="<?= $isDanceEvent ? '7' : '6' ?>" class="cms-empty-state">No artists found for this event.</td>
                                </tr>
                            <?php else: ?>
                                <?php $counter = 1; ?>
                                <?php foreach ($artistRows as $artiste): ?>
                                    <?php $artistMedia = $danceArtistMediaById[$artiste->id] ?? null; ?>
                                    <tr>
                                        <td><?= $counter++ ?></td>
                                        <td><?= htmlspecialchars($artiste->performerName ?? 'Unknown') ?></td>
                                        <td><?= htmlspecialchars($artiste->performerType ?? 'Unknown') ?></td>
                                        <td><?= htmlspecialchars(Event::from($artiste->eventId)->label() ?? 'Unknown') ?></td>
                                        <td><?= htmlspecialchars($artiste->description ?? 'No description') ?></td>
                                        <?php if ($isDanceEvent): ?>
                                            <td style="min-width: 300px;">
                                                <?php if (is_array($artistMedia) && (int)($artistMedia['artistSectionItemId'] ?? 0) > 0): ?>
                                                    <input type="hidden" class="performer-artist-item-id" value="<?= (int)$artistMedia['artistSectionItemId'] ?>">
                                                    <input type="hidden" class="performer-artist-image" value="<?= htmlspecialchars((string)($artistMedia['artistImagePath'] ?? '')) ?>">
                                                    <div class="d-flex flex-wrap gap-2 align-items-center performer-image-row">
                                                        <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                                                        <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                                                        <a
                                                            href="<?= htmlspecialchars((string)($artistMedia['artistImagePath'] ?? '')) ?>"
                                                            class="btn btn-sm btn-outline-secondary performer-download-link<?= empty($artistMedia['artistImagePath']) ? ' d-none' : '' ?>"
                                                            download
                                                        >
                                                            Download
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted small">No linked image slot</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                        <td>
                                            <div class="cms-table-actions">
                                                <a href="/cms/eventManagement/artists/edit?event_id=<?= $eventid ?>&id=<?= $artiste->id  ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <a href="/cms/eventManagement/artists/delete?event_id=<?= $eventid ?>&id=<?= $artiste->id  ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this artist? This cannot be undone.')">Delete</a>
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

<?php if ($isDanceEvent): ?>
    <?php include __DIR__ . '/../../partialsViews/cms/upload-feedback-modal.php'; ?>
    <script src="/js/cms/upload-feedback.js"></script>
    <script src="/js/cms/media-upload.js"></script>
    <script src="/js/cms/dance-artist-management.js"></script>
<?php endif; ?>
