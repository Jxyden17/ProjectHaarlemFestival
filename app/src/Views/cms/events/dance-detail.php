<?php
use App\Models\ViewModels\Cms\Dance\DanceDetailEditViewModel;
use App\Models\ViewModels\Cms\Dance\DanceDetailHeroImageRowViewModel;
use App\Models\ViewModels\Cms\Dance\DanceDetailHighlightRowViewModel;
use App\Models\ViewModels\Cms\Dance\DanceDetailTrackRowViewModel;

$contentViewModel = (isset($contentViewModel) && $contentViewModel instanceof DanceDetailEditViewModel)
    ? $contentViewModel
    : new DanceDetailEditViewModel('', 'Dance Detail Content', '', '', '', '', '', '', [], '', [], '', '', [], '', '');
$pageSlug = trim((string)$contentViewModel->pageSlug);
$encodedPageSlug = rawurlencode($pageSlug);
$heroImages = $contentViewModel->heroImages;
$highlights = $contentViewModel->highlights;
$tracks = $contentViewModel->tracks;
$formAction = $pageSlug === '' ? '/cms/events/dance-detail' : '/cms/events/dance-detail/' . $encodedPageSlug;

// Builds upload module names for this page slug, or returns an empty string when no slug is available.
$buildModuleName = static function (string $prefix) use ($pageSlug): string {
    return $pageSlug === '' ? '' : $prefix . ':' . $pageSlug;
};
$detailMediaModule = $buildModuleName('dance_detail_hero');
$detailTrackMediaModule = $buildModuleName('dance_detail_track');
$detailTrackAudioModule = $buildModuleName('dance_detail_track_audio');
?>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

<div class="container-lg py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-1"><?= htmlspecialchars($contentViewModel->editorTitle) ?></h1>
            <p class="mb-0">Public page: <a href="<?= htmlspecialchars($contentViewModel->publicPath) ?>" target="_blank" rel="noreferrer"><?= htmlspecialchars($contentViewModel->publicPath) ?></a></p>
        </div>
        <a href="/cms/events" class="btn btn-outline-secondary">Back to Events</a>
    </div>

    <form method="POST" action="<?= htmlspecialchars($formAction) ?>" class="card border-0 shadow-sm" data-quill-form="1" data-image-upload-module="<?= htmlspecialchars($detailMediaModule) ?>" data-debug-enabled="<?= $isDebugMode ? '1' : '0' ?>" data-save-api="/cms/events/dance-detail/<?= $encodedPageSlug ?>/updateAPI">
        <div class="card-body p-4">
            <div class="accordion" id="danceDetailAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="dance-detail-page-heading">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#dance-detail-page-panel" aria-expanded="true" aria-controls="dance-detail-page-panel">
                            Page
                        </button>
                    </h2>
                    <div id="dance-detail-page-panel" class="accordion-collapse collapse show" aria-labelledby="dance-detail-page-heading" data-bs-parent="#danceDetailAccordion">
                        <div class="accordion-body p-4">
                            <div class="mb-0">
                                <label for="page_title" class="form-label">Browser Tab Title</label>
                                <input type="text" id="page_title" name="page_title" class="form-control" value="<?= htmlspecialchars($contentViewModel->pageTitle) ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="dance-detail-hero-heading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dance-detail-hero-panel" aria-expanded="false" aria-controls="dance-detail-hero-panel">
                            Hero
                        </button>
                    </h2>
                    <div id="dance-detail-hero-panel" class="accordion-collapse collapse" aria-labelledby="dance-detail-hero-heading" data-bs-parent="#danceDetailAccordion">
                        <div class="accordion-body p-4">
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
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="dance-detail-highlights-heading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dance-detail-highlights-panel" aria-expanded="false" aria-controls="dance-detail-highlights-panel">
                            Highlights
                        </button>
                    </h2>
                    <div id="dance-detail-highlights-panel" class="accordion-collapse collapse" aria-labelledby="dance-detail-highlights-heading" data-bs-parent="#danceDetailAccordion">
                        <div class="accordion-body p-4">
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
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="dance-detail-tracks-heading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dance-detail-tracks-panel" aria-expanded="false" aria-controls="dance-detail-tracks-panel">
                            Tracks
                        </button>
                    </h2>
                    <div id="dance-detail-tracks-panel" class="accordion-collapse collapse" aria-labelledby="dance-detail-tracks-heading" data-bs-parent="#danceDetailAccordion">
                        <div class="accordion-body p-4">
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
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="dance-detail-info-heading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dance-detail-info-panel" aria-expanded="false" aria-controls="dance-detail-info-panel">
                            Important Information
                        </button>
                    </h2>
                    <div id="dance-detail-info-panel" class="accordion-collapse collapse" aria-labelledby="dance-detail-info-heading" data-bs-parent="#danceDetailAccordion">
                        <div class="accordion-body p-4">
                            <div class="mb-3">
                                <label for="important_information_title" class="form-label">Title</label>
                                <input type="text" id="important_information_title" name="important_information_title" class="form-control" value="<?= htmlspecialchars($contentViewModel->importantInformationTitle) ?>" required>
                            </div>
                            <div class="mb-0">
                                <label for="important_information_html" class="form-label">Content (HTML/Text)</label>
                                <textarea id="important_information_html" name="important_information_html" class="form-control" data-quill="1" rows="6" required><?= htmlspecialchars($contentViewModel->importantInformationHtml) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-primary save-btn">Save Content</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../partialsViews/cms/upload-feedback-modal.php'; ?>

<script src="/js/cms/upload-feedback.js"></script>
<script src="/js/cms/media-upload.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script src="/js/cms/page-editor.js"></script>
<script src="/js/cms/form-save-api.js"></script>
<script src="/js/cms/dance-detail.js"></script>
