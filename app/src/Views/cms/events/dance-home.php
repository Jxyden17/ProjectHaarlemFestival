<?php
$contentData = $contentData ?? [];
$artists = is_array($contentData['artists'] ?? null) ? $contentData['artists'] : [];
$passes = is_array($contentData['passes'] ?? null) ? $contentData['passes'] : [];
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Dance Home Content</h1>
        <a href="/cms/events" class="btn btn-outline-secondary">Back to Events</a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success" role="alert">
            Dance home content updated.
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars((string)$error) ?>
        </div>
    <?php endif; ?>

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
                    value="<?= htmlspecialchars((string)($contentData['schedule_title'] ?? '')) ?>"
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
                    value="<?= htmlspecialchars((string)($contentData['banner_badge'] ?? '')) ?>"
                >
            </div>

            <div class="mb-3">
                <label for="banner_title" class="form-label">Banner Title</label>
                <input
                    type="text"
                    id="banner_title"
                    name="banner_title"
                    class="form-control"
                    value="<?= htmlspecialchars((string)($contentData['banner_title'] ?? '')) ?>"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="banner_description" class="form-label">Banner Description</label>
                <textarea
                    id="banner_description"
                    name="banner_description"
                    class="form-control"
                    rows="4"
                    required
                ><?= htmlspecialchars((string)($contentData['banner_description'] ?? '')) ?></textarea>
            </div>

            <hr>

            <h2 class="h5 mb-2">Featured Artists</h2>
            <div class="mb-3">
                <label for="artists_title" class="form-label">Artists Section Title</label>
                <input
                    type="text"
                    id="artists_title"
                    name="artists_title"
                    class="form-control"
                    value="<?= htmlspecialchars((string)($contentData['artists_title'] ?? '')) ?>"
                    required
                >
            </div>
            <div id="artists-container">
                <?php foreach ($artists as $index => $artist): ?>
                    <div class="border rounded p-3 mb-2 artist-row">
                        <div class="mb-2">
                            <label class="form-label">Artist Name</label>
                            <input type="text" name="artists[<?= (int)$index ?>][name]" class="form-control artist-name" value="<?= htmlspecialchars((string)($artist['name'] ?? '')) ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Genre</label>
                            <input type="text" name="artists[<?= (int)$index ?>][genre]" class="form-control artist-genre" value="<?= htmlspecialchars((string)($artist['genre'] ?? '')) ?>">
                        </div>
                        <input type="hidden" name="artists[<?= (int)$index ?>][id]" class="artist-item-id" value="<?= (int)($artist['id'] ?? 0) ?>">
                        <input type="hidden" name="artists[<?= (int)$index ?>][image]" class="artist-image" value="<?= htmlspecialchars((string)($artist['image'] ?? '')) ?>">
                        <div class="mb-2">
                            <label class="form-label">Replace Image</label>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <input type="file" class="form-control artist-upload-input" accept="image/jpeg,image/png,image/webp">
                                <button type="button" class="btn btn-sm btn-outline-primary upload-artist-image">Upload</button>
                                <a
                                    href="<?= htmlspecialchars((string)($artist['image'] ?? '')) ?>"
                                    class="btn btn-sm btn-outline-secondary artist-download-link<?= empty($artist['image']) ? ' d-none' : '' ?>"
                                    download
                                >
                                    Download
                                </a>
                            </div>
                            <div class="form-text">Upload replaces the same artist image file on the server.</div>
                        </div>
                    </div>
                <?php endforeach; ?>
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
                    value="<?= htmlspecialchars((string)($contentData['important_information_title'] ?? '')) ?>"
                    required
                >
            </div>
            <div class="mb-3">
                <label for="important_information_html" class="form-label">Content (HTML/Text)</label>
                <textarea
                    id="important_information_html"
                    name="important_information_html"
                    class="form-control"
                    rows="6"
                    required
                ><?= htmlspecialchars((string)($contentData['important_information_html'] ?? '')) ?></textarea>
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
                    value="<?= htmlspecialchars((string)($contentData['passes_title'] ?? '')) ?>"
                    required
                >
            </div>
            <div id="passes-container">
                <?php foreach ($passes as $index => $pass): ?>
                    <div class="border rounded p-3 mb-2 pass-row">
                        <div class="mb-2">
                            <label class="form-label">Label</label>
                            <input type="text" name="passes[<?= (int)$index ?>][label]" class="form-control pass-label" value="<?= htmlspecialchars((string)($pass['label'] ?? '')) ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Price</label>
                            <input type="text" name="passes[<?= (int)$index ?>][price]" class="form-control pass-price" value="<?= htmlspecialchars((string)($pass['price'] ?? '')) ?>">
                        </div>
                        <div class="form-check mb-2">
                            <input
                                type="checkbox"
                                class="form-check-input pass-highlight"
                                name="passes[<?= (int)$index ?>][highlight]"
                                value="1"
                                <?= !empty($pass['highlight']) ? 'checked' : '' ?>
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
                    value="<?= htmlspecialchars((string)($contentData['capacity_title'] ?? '')) ?>"
                    required
                >
            </div>
            <div class="mb-3">
                <label for="capacity_html" class="form-label">Content (HTML/Text)</label>
                <textarea
                    id="capacity_html"
                    name="capacity_html"
                    class="form-control"
                    rows="6"
                    required
                ><?= htmlspecialchars((string)($contentData['capacity_html'] ?? '')) ?></textarea>
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
                    value="<?= htmlspecialchars((string)($contentData['special_title'] ?? '')) ?>"
                    required
                >
            </div>
            <div class="mb-3">
                <label for="special_html" class="form-label">Content (HTML/Text)</label>
                <textarea
                    id="special_html"
                    name="special_html"
                    class="form-control"
                    rows="6"
                    required
                ><?= htmlspecialchars((string)($contentData['special_html'] ?? '')) ?></textarea>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Save Content</button>
        </div>
    </form>
</div>

<?php $danceHomeJsVersion = @filemtime(__DIR__ . '/../../../../public/js/cms/dance-home.js') ?: time(); ?>
<script src="/js/cms/dance-home.js?v=<?= (int)$danceHomeJsVersion ?>"></script>
