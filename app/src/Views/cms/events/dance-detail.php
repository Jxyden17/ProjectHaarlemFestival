<?php
use App\Models\ViewModels\Cms\Dance\DanceDetailContentViewModel;
use App\Models\ViewModels\Cms\Dance\DanceDetailHeroImageRowViewModel;
use App\Models\ViewModels\Cms\Dance\DanceDetailHighlightRowViewModel;
use App\Models\ViewModels\Cms\Dance\DanceDetailTrackRowViewModel;

$contentViewModel = (isset($contentViewModel) && $contentViewModel instanceof DanceDetailContentViewModel)
    ? $contentViewModel
    : new DanceDetailContentViewModel('', 'Dance Detail Content', '', '', '', '', '', [], '', [], '', '', [], '', '');
$heroImages = $contentViewModel->heroImages;
$highlights = $contentViewModel->highlights;
$tracks = $contentViewModel->tracks;
$formAction = (string)($formAction ?? '/cms/events/dance-detail');
$detailMediaModule = $contentViewModel->detailSlug === '' ? '' : 'dance_detail_hero:' . $contentViewModel->detailSlug;
$detailTrackMediaModule = $contentViewModel->detailSlug === '' ? '' : 'dance_detail_track:' . $contentViewModel->detailSlug;
$detailTrackAudioModule = $contentViewModel->detailSlug === '' ? '' : 'dance_detail_track_audio:' . $contentViewModel->detailSlug;
?>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

<div class="container-lg py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-1"><?= htmlspecialchars($contentViewModel->editorTitle) ?></h1>
            <p class="text-muted mb-0">Public page: <a href="<?= htmlspecialchars($contentViewModel->publicPath) ?>" target="_blank" rel="noreferrer"><?= htmlspecialchars($contentViewModel->publicPath) ?></a></p>
        </div>
        <a href="/cms/events" class="btn btn-outline-secondary">Back to Events</a>
    </div>

    <?php
    $successMessage = 'Dance detail content updated.';
    include __DIR__ . '/../../partialsViews/cms/form-feedback.php';
    ?>

    <form method="POST" action="<?= htmlspecialchars($formAction) ?>" class="card border-0 shadow-sm" data-quill-form="1" data-image-upload-module="<?= htmlspecialchars($detailMediaModule) ?>">
        <div class="card-body p-4">
            <h2 class="h5">Hero</h2>
            <div class="mb-3">
                <label for="hero_title" class="form-label">Performer Name</label>
                <input type="hidden" id="hero_title" name="hero_title" value="<?= htmlspecialchars($contentViewModel->heroTitle) ?>">
                <div class="form-control-plaintext border rounded px-3 py-2 bg-body-tertiary"><?= htmlspecialchars($contentViewModel->heroTitle) ?></div>
                <div class="form-text">The public heading comes from the schedule performer record. Change it in the schedule editor if needed.</div>
            </div>
            <div class="mb-3">
                <label for="hero_badge" class="form-label">Badge</label>
                <input type="text" id="hero_badge" name="hero_badge" class="form-control" value="<?= htmlspecialchars($contentViewModel->heroBadge) ?>">
            </div>
            <div class="mb-3">
                <label for="hero_subtitle" class="form-label">Subtitle</label>
                <textarea id="hero_subtitle" name="hero_subtitle" class="form-control" rows="3" required><?= htmlspecialchars($contentViewModel->heroSubtitle) ?></textarea>
            </div>

            <h3 class="h6 mt-4">Hero Images</h3>
            <?php foreach ($heroImages as $index => $image): ?>
                <?php if (!$image instanceof DanceDetailHeroImageRowViewModel) { continue; } ?>
                <div class="border rounded p-3 mb-2" data-image-upload-module="<?= htmlspecialchars($detailMediaModule) ?>">
                    <input type="hidden" name="hero_images[<?= (int)$index ?>][id]" value="<?= (int)$image->id ?>">
                    <div class="mb-3">
                        <label class="form-label">Hero Image</label>
                        <input type="hidden" class="performer-artist-item-id" value="<?= (int)$image->id ?>">
                        <input type="hidden" class="performer-artist-image" name="hero_images[<?= (int)$index ?>][image]" value="<?= htmlspecialchars($image->image) ?>">
                        <div class="d-flex flex-wrap gap-2 align-items-center performer-image-row">
                            <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                            <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                            <a
                                href="<?= htmlspecialchars($image->image) ?>"
                                class="btn btn-sm btn-outline-secondary performer-download-link<?= $image->image === '' ? ' d-none' : '' ?>"
                                download
                            >
                                Download
                            </a>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Alt Text</label>
                        <input type="text" class="form-control" name="hero_images[<?= (int)$index ?>][alt]" value="<?= htmlspecialchars($image->alt) ?>">
                    </div>
                </div>
            <?php endforeach; ?>

            <hr>

            <h2 class="h5">Highlights</h2>
            <div class="mb-3">
                <label for="highlights_title" class="form-label">Section Title</label>
                <input type="text" id="highlights_title" name="highlights_title" class="form-control" value="<?= htmlspecialchars($contentViewModel->highlightsTitle) ?>" required>
            </div>
            <?php foreach ($highlights as $index => $highlight): ?>
                <?php if (!$highlight instanceof DanceDetailHighlightRowViewModel) { continue; } ?>
                <div class="border rounded p-3 mb-2">
                    <input type="hidden" name="highlights[<?= (int)$index ?>][id]" value="<?= (int)$highlight->id ?>">
                    <div class="mb-2">
                        <label class="form-label">Icon</label>
                        <input type="text" class="form-control" name="highlights[<?= (int)$index ?>][icon]" value="<?= htmlspecialchars($highlight->icon) ?>" placeholder="star">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="highlights[<?= (int)$index ?>][title]" value="<?= htmlspecialchars($highlight->title) ?>" required>
                    </div>
                    <div>
                        <label class="form-label">Content</label>
                        <textarea class="form-control" name="highlights[<?= (int)$index ?>][content]" rows="2" required><?= htmlspecialchars($highlight->content) ?></textarea>
                    </div>
                </div>
            <?php endforeach; ?>

            <hr>

            <h2 class="h5">Tracks</h2>
            <div class="mb-3">
                <label for="tracks_title" class="form-label">Section Title</label>
                <input type="text" id="tracks_title" name="tracks_title" class="form-control" value="<?= htmlspecialchars($contentViewModel->tracksTitle) ?>" required>
            </div>
            <div class="mb-3">
                <label for="tracks_note" class="form-label">Tracks Note</label>
                <textarea id="tracks_note" name="tracks_note" class="form-control" rows="2"><?= htmlspecialchars($contentViewModel->tracksNote) ?></textarea>
            </div>
            <?php foreach ($tracks as $index => $track): ?>
                <?php if (!$track instanceof DanceDetailTrackRowViewModel) { continue; } ?>
                <div
                    class="border rounded p-3 mb-2"
                    data-image-upload-module="<?= htmlspecialchars($detailTrackMediaModule) ?>"
                    data-audio-upload-module="<?= htmlspecialchars($detailTrackAudioModule) ?>"
                >
                    <input type="hidden" name="tracks[<?= (int)$index ?>][id]" value="<?= (int)$track->id ?>">
                    <div class="mb-2">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="tracks[<?= (int)$index ?>][title]" value="<?= htmlspecialchars($track->title) ?>" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Subtitle</label>
                        <input type="text" class="form-control" name="tracks[<?= (int)$index ?>][subtitle]" value="<?= htmlspecialchars($track->subtitle) ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Year / Metadata</label>
                        <input type="text" class="form-control" name="tracks[<?= (int)$index ?>][year]" value="<?= htmlspecialchars($track->year) ?>">
                    </div>
                    <div>
                        <label class="form-label">Track Image</label>
                        <input type="hidden" class="performer-artist-item-id" value="<?= (int)$track->id ?>">
                        <input type="hidden" class="performer-artist-image" name="tracks[<?= (int)$index ?>][image]" value="<?= htmlspecialchars($track->image) ?>">
                        <div class="d-flex flex-wrap gap-2 align-items-center performer-image-row">
                            <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                            <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                            <a
                                href="<?= htmlspecialchars($track->image) ?>"
                                class="btn btn-sm btn-outline-secondary performer-download-link<?= $track->image === '' ? ' d-none' : '' ?>"
                                download
                            >
                                Download
                            </a>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Track Audio</label>
                        <input type="hidden" class="performer-track-audio" name="tracks[<?= (int)$index ?>][audio_url]" value="<?= htmlspecialchars($track->audioUrl) ?>">
                        <div class="d-flex flex-wrap gap-2 align-items-center performer-audio-row">
                            <input type="file" class="form-control form-control-sm performer-upload-audio-input" accept="audio/mpeg,audio/wav,audio/ogg,audio/mp4,audio/x-m4a">
                            <button type="button" class="btn btn-sm btn-outline-primary upload-performer-audio">Upload</button>
                            <a
                                href="<?= htmlspecialchars($track->audioUrl) ?>"
                                class="btn btn-sm btn-outline-secondary performer-audio-download-link<?= $track->audioUrl === '' ? ' d-none' : '' ?>"
                                download
                            >
                                Download
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <hr>

            <h2 class="h5">Important Information</h2>
            <div class="mb-3">
                <label for="important_information_title" class="form-label">Title</label>
                <input type="text" id="important_information_title" name="important_information_title" class="form-control" value="<?= htmlspecialchars($contentViewModel->importantInformationTitle) ?>" required>
            </div>
            <div class="mb-3">
                <label for="important_information_html" class="form-label">Content (HTML/Text)</label>
                <textarea id="important_information_html" name="important_information_html" class="form-control" data-quill="1" rows="6" required><?= htmlspecialchars($contentViewModel->importantInformationHtml) ?></textarea>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-primary save-btn">Save Content</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<?php $danceDetailJsVersion = @filemtime(__DIR__ . '/../../../../public/js/cms/dance-detail.js') ?: time(); ?>
<script src="/js/cms/dance-detail.js?v=<?= (int)$danceDetailJsVersion ?>"></script>
