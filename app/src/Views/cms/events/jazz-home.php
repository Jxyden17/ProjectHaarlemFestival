<?php 
use App\Models\ViewModels\Cms\Jazz\JazzHomeEditViewModel;
$contentViewModel = (isset($contentViewModel) && $contentViewModel instanceof JazzHomeEditViewModel)
    ? $contentViewModel
    : new JazzHomeEditViewModel('', '', '', '', []);
$passes = $contentViewModel->passes;

?>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

<div class="container-lg py-4 py-md-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-1">Jazz Home Content</h1>
            <p class="text-muted mb-0">Update the Jazz home page content.</p>
        </div>
        <a href="/cms/events" class="btn btn-outline-secondary">Back to Events</a>
    </div>

    <form method="POST" action="/cms/events/jazz-home" class="card border-0 shadow-sm" data-quill-form="1" data-save-api="/cms/events/Jazz-homeAPI">
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

            <h2 class="h5">Featured Artists & Schedule</h2>
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
                <?php foreach ($passes as $pass): ?>
                    <?php if (!$pass instanceof App\Models\ViewModels\Cms\Jazz\JazzHomePassRowViewModel) { continue; } ?>
                    <div class="border rounded p-3 mb-2 pass-row">
                        <div class="mb-2">
                            <label class="form-label">Label</label>
                            <input type="text" name="passes[<?= (int)$pass->label ?>][label]" class="form-control pass-label" value="<?= htmlspecialchars($pass->label) ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Price</label>
                            <input type="text" name="passes[<?= (int)$pass->price ?>][price]" class="form-control pass-price" value="<?= htmlspecialchars($pass->price) ?>">
                        </div>
                        <input type="hidden" name="passes[<?= (int)$pass->id ?>][id]" value="<?= (int)$pass->id ?>">
                        <div class="form-check mb-2">
                            <input
                                type="checkbox"
                                class="form-check-input pass-highlight"
                                name="passes[<?= (int)$pass->highligh ?>][highlight]"
                                value="1"
                                <?= $pass->highlight ? 'checked' : '' ?>
                            >
                            <label class="form-check-label">Highlighted row</label>
                        </div>
                    </div>
                <?php endforeach; ?>
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
