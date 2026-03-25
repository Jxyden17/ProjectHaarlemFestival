<?php 
use App\Models\ViewModels\Cms\Dance\DanceHomeEditViewModel;
$contentViewModel = (isset($contentViewModel) && $contentViewModel instanceof DanceHomeEditViewModel)
    ? $contentViewModel
    : new DanceHomeEditViewModel('', '', '', '', '', '', '', '', '', [], '', '', '', '');
$passes = $contentViewModel->passes;
?>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

<div class="container-lg py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-1">Jazz Home Content</h1>
            <p class="text-muted mb-0">Update the public Jazz landing page content and rich-text sections.</p>
        </div>
        <a href="/cms/events" class="btn btn-outline-secondary">Back to Events</a>
    </div>

    <form method="POST" action="/cms/events/Jazz-home" class="card border-0 shadow-sm" data-quill-form="1" data-save-api="/cms/events/Jazz-homeAPI">
        <div class="card-body p-4">
            <h2 class="h5">Page</h2>
            <div class="mb-3">
                <label for="page_title" class="form-label">Browser Tab Title</label>
                <input
                    type="text"
                    id="page_title"
                    name="page_title"
                    class="form-control"
                    value="<?= htmlspecialchars($contentViewModel->pageTitle) ?>"
                    required
                >
            </div>

            <h2 class="h5">Banner</h2>
            <div class="mb-3">
                <label for="banner_badge" class="form-label">Banner Badge</label>
                <input
                    type="text"
                    id="banner_badge"
                    name="banner_badge"
                    class="form-control"
                    value="<?= htmlspecialchars($contentViewModel->bannerBadge) ?>"
                >
            </div>

            <div class="mb-3">
                <label for="banner_title" class="form-label">Banner Title</label>
                <input
                    type="text"
                    id="banner_title"
                    name="banner_title"
                    class="form-control"
                    value="<?= htmlspecialchars($contentViewModel->bannerTitle) ?>"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="banner_description" class="form-label">Banner Description</label>
                <textarea
                    id="banner_description"
                    name="banner_description"
                    class="form-control"
                    data-quill="1"
                    rows="4"
                    required
                ><?= htmlspecialchars($contentViewModel->bannerDescription) ?></textarea>
            </div>
            <hr>

            <h2 class="h5">Featured Artists & Schedule</h2>
            <div class="alert alert-info" role="alert">
                This section controls the featured artists title and the schedule title. Performer names, types, descriptions, schedule sessions, and artist images are managed in the
                <a href="/cms/events/dance-schedule" class="alert-link">Dance Schedule editor</a>.
            </div>
            <div class="mb-3">
                <label for="featured_artists_title" class="form-label">Featured Artists Title</label>
                <input
                    type="text"
                    id="featured_artists_title"
                    name="featured_artists_title"
                    class="form-control"
                    value="<?= htmlspecialchars($contentViewModel->featuredArtistsTitle) ?>"
                    required
                >
            </div>
            <div class="mb-3">
                <label for="schedule_title" class="form-label">Schedule Title</label>
                <input
                    type="text"
                    id="schedule_title"
                    name="schedule_title"
                    class="form-control"
                    value="<?= htmlspecialchars($contentViewModel->scheduleTitle) ?>"
                    required
                >
            </div>

            <hr>

            <h2 class="h5">Important Information</h2>
            <div class="mb-3">
                <label for="important_information_title" class="form-label">Title</label>
                <input
                    type="text"
                    id="important_information_title"
                    name="important_information_title"
                    class="form-control"
                    value="<?= htmlspecialchars($contentViewModel->importantInformationTitle) ?>"
                    required
                >
            </div>
            <div class="mb-3">
                <label for="important_information_html" class="form-label">Content (HTML/Text)</label>
                <textarea
                    id="important_information_html"
                    name="important_information_html"
                    class="form-control"
                    data-quill="1"
                    rows="6"
                    required
                ><?= htmlspecialchars($contentViewModel->importantInformationHtml) ?></textarea>
            </div>

            <hr>

            <h2 class="h5 mb-2">All-Access Passes</h2>
            <div class="mb-3">
                <label for="passes_title" class="form-label">Passes Section Title</label>
                <input
                    type="text"
                    id="passes_title"
                    name="passes_title"
                    class="form-control"
                    value="<?= htmlspecialchars($contentViewModel->passesTitle) ?>"
                    required
                >
            </div>
            <div id="passes-container">
                <?php foreach ($passes as $index => $pass): ?>
                    <?php if (!$pass instanceof \App\Models\ViewModels\Cms\Dance\DanceHomePassRowViewModel) { continue; } ?>
                    <div class="border rounded p-3 mb-2 pass-row">
                        <div class="mb-2">
                            <label class="form-label">Label</label>
                            <input type="text" name="passes[<?= (int)$index ?>][label]" class="form-control pass-label" value="<?= htmlspecialchars($pass->label) ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Price</label>
                            <input type="text" name="passes[<?= (int)$index ?>][price]" class="form-control pass-price" value="<?= htmlspecialchars($pass->price) ?>">
                        </div>
                        <input type="hidden" name="passes[<?= (int)$index ?>][id]" value="<?= (int)$pass->id ?>">
                        <div class="form-check mb-2">
                            <input
                                type="checkbox"
                                class="form-check-input pass-highlight"
                                name="passes[<?= (int)$index ?>][highlight]"
                                value="1"
                                <?= $pass->highlight ? 'checked' : '' ?>
                            >
                            <label class="form-check-label">Highlighted row</label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <hr>

            <h2 class="h5">Capacity & Entry</h2>
            <div class="mb-3">
                <label for="capacity_title" class="form-label">Title</label>
                <input
                    type="text"
                    id="capacity_title"
                    name="capacity_title"
                    class="form-control"
                    value="<?= htmlspecialchars($contentViewModel->capacityTitle) ?>"
                    required
                >
            </div>
            <div class="mb-3">
                <label for="capacity_html" class="form-label">Content (HTML/Text)</label>
                <textarea
                    id="capacity_html"
                    name="capacity_html"
                    class="form-control"
                    data-quill="1"
                    rows="6"
                    required
                ><?= htmlspecialchars($contentViewModel->capacityHtml) ?></textarea>
            </div>

            <hr>

            <h2 class="h5">Special Session</h2>
            <div class="mb-3">
                <label for="special_title" class="form-label">Title</label>
                <input
                    type="text"
                    id="special_title"
                    name="special_title"
                    class="form-control"
                    value="<?= htmlspecialchars($contentViewModel->specialTitle) ?>"
                    required
                >
            </div>
            <div class="mb-3">
                <label for="special_html" class="form-label">Content (HTML/Text)</label>
                <textarea
                    id="special_html"
                    name="special_html"
                    class="form-control"
                    data-quill="1"
                    rows="6"
                    required
                ><?= htmlspecialchars($contentViewModel->specialHtml) ?></textarea>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-primary save-btn">Save Content</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../partialsViews/cms/upload-feedback-modal.php'; ?>

<script src="/js/cms/upload-feedback.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script src="/js/cms/page-editor.js"></script>
<script src="/js/cms/form-save-api.js"></script>
<script src="/js/cms/dance-home.js"></script>
