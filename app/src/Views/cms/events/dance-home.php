<?php 
use App\Models\ViewModels\Cms\Dance\DanceHomeContentViewModel;
$contentViewModel = (isset($contentViewModel) && $contentViewModel instanceof DanceHomeContentViewModel)
    ? $contentViewModel
    : new DanceHomeContentViewModel('', '', '', '', '', [], '', '', '', [], '', '', '', '');
$artists = $contentViewModel->artists;
$passes = $contentViewModel->passes;
?>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Dance Home Content</h1>
        <a href="/cms/events" class="btn btn-outline-secondary">Back to Events</a>
    </div>

    <?php
    $successMessage = 'Dance home content updated.';
    include __DIR__ . '/../../partialsViews/cms/form-feedback.php';
    ?>

    <form method="POST" action="/cms/events/dance-home" class="card">
        <div class="card-body">
            <h2 class="h5">Schedule</h2>
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

            <input type="hidden" name="artists_title" value="<?= htmlspecialchars($contentViewModel->artistsTitle !== '' ? $contentViewModel->artistsTitle : 'Featured Artists') ?>">
            <?php foreach ($artists as $index => $artist): ?>
                <?php if (!$artist instanceof \App\Models\ViewModels\Cms\Dance\DanceHomeArtistRowViewModel) { continue; } ?>
                <input type="hidden" name="artists[<?= (int)$index ?>][id]" value="<?= (int)$artist->id ?>">
                <input type="hidden" name="artists[<?= (int)$index ?>][name]" value="<?= htmlspecialchars($artist->name) ?>">
                <input type="hidden" name="artists[<?= (int)$index ?>][genre]" value="<?= htmlspecialchars($artist->genre) ?>">
                <input type="hidden" name="artists[<?= (int)$index ?>][image]" value="<?= htmlspecialchars($artist->image) ?>">
            <?php endforeach; ?>

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
            <button type="submit" class="btn btn-primary">Save Content</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<?php $danceHomeJsVersion = @filemtime(__DIR__ . '/../../../../public/js/cms/dance-home.js') ?: time(); ?>
<script src="/js/cms/dance-home.js?v=<?= (int)$danceHomeJsVersion ?>"></script>
