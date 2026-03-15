<?php
$heroItems = $hero?->items ?? [];
$stopsItems = $stops?->items ?? [];
$discoverGridItems = $discover?->getItemsByCategorie('grid') ?? [];
$discoverPriceItems = $discover?->getItemsByCategorie('price') ?? [];
$discoverInfoItems = $discover?->getItemsByCategorie('info') ?? [];
$guideItems = $guide?->getItemsByCategorie('guide') ?? [];
$pageSlug = (string)($pageSlug ?? 'tour-home');
?>

<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Tour Home Content</h1>
        <a href="/cms/events" class="btn btn-outline-secondary">Back to Events</a>
    </div>

    <?php
    $successMessage = 'Tour home content updated.';
    include __DIR__ . '/../../partialsViews/cms/form-feedback.php';
    ?>

    <form method="POST" action="/cms/events/tour-home" class="card" data-tour-page-slug="<?= htmlspecialchars($pageSlug) ?>">
        <div class="card-body">
            <h2 class="h2">Header</h2>
            <div class="mb-3">
                <label for="hero_title" class="form-label">Title</label>
                <input id="hero_title" name="sections[hero][title]" class="form-control" rows="2" value="<?= htmlspecialchars($hero?->title) ?>">
            </div>

            <div class="mb-3">
                <label for="hero_subtitle" class="form-label">SubTitle</label>
                <input id="hero_subtitle" name="sections[hero][subtitle]" class="form-control" rows="2" value="<?= htmlspecialchars($hero?->subTitle) ?>">
                <input type="hidden" name="sections[hero][description]" value="<?= htmlspecialchars($hero?->description) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Fotos of Header</label>
                <?php foreach ($heroItems as $index => $item): ?>
                    <div class="d-flex flex-wrap gap-2 align-items-center performer-image-row mb-2" data-tour-upload-row="1" data-tour-section-type="hero" data-tour-item-category="<?= htmlspecialchars((string)($item->category ?? '')) ?>">
                        <input type="hidden" name="items[hero][<?= (int)$index ?>][id]" class="tour-item-id" value="<?= (int)($item->id) ?>">
                        <input type="hidden" name="items[hero][<?= (int)$index ?>][image_path]" class="performer-artist-image" value="<?= htmlspecialchars($item->image ?? '') ?>">
                        <input type="hidden" name="items[hero][<?= (int)$index ?>][title]" value="<?= htmlspecialchars($item->title ?? '') ?>">
                        <input type="hidden" name="items[hero][<?= (int)$index ?>][content]" value="<?= htmlspecialchars($item->content ?? '') ?>">
                        <input type="hidden" name="items[hero][<?= (int)$index ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                        <input type="hidden" name="items[hero][<?= (int)$index ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                        <input type="hidden" name="items[hero][<?= (int)$index ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                        <input type="hidden" name="items[hero][<?= (int)$index ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
                        <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                        <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                        <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                    </div>
                <?php endforeach; ?>
            </div>

            <h2>Stops on the tour</h2>
            <div class="mb-3">
                <label for="tour_overview_title"  class="form-label">Title</label>
                <input id="tour_overview_title" name="sections[tour_overview][title]" class="form-control" rows="2" value="<?= htmlspecialchars($stops?->title) ?>">
                <input type="hidden" name="sections[tour_overview][subtitle]" value="<?= htmlspecialchars($stops?->subTitle ?? '') ?>">
                <input type="hidden" name="sections[tour_overview][description]" value="<?= htmlspecialchars($stops?->description ?? '') ?>">
            </div>

            <?php foreach ($stopsItems as $index => $item): ?>
                <div class="mb-3">
                    <h4><?= htmlspecialchars($item->title ?? '') ?></h4>
                    <input type="hidden" name="items[tour_overview][<?= (int)$index ?>][id]" class="tour-item-id" value="<?= (int)($item->id) ?>">
                    <input type="hidden" name="items[tour_overview][<?= (int)$index ?>][image_path]" class="performer-artist-image" value="<?= htmlspecialchars($item->image ?? '') ?>">
                    <label class="form-label">Letter</label>
                    <input type="text" name="items[tour_overview][<?= (int)$index ?>][icon_class]" class="form-control mb-2" value="<?= htmlspecialchars($item->icon ?? '') ?>">

                    <label class="form-label">Title</label>
                    <input type="text" name="items[tour_overview][<?= (int)$index ?>][title]" class="form-control mb-2" value="<?= htmlspecialchars($item->title ?? '') ?>">

                    <label class="form-label">Subtitle</label>
                    <input type="text" name="items[tour_overview][<?= (int)$index ?>][item_subtitle]" class="form-control mb-2" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">

                    <label class="form-label">Duration</label>
                    <input type="text" name="items[tour_overview][<?= (int)$index ?>][duration]" class="form-control mb-2" value="<?= htmlspecialchars($item->duration ?? '') ?>">

                    <label class="form-label">Content</label>
                    <textarea name="items[tour_overview][<?= (int)$index ?>][content]" data-quill="1" class="form-control mb-2" rows="3"><?= htmlspecialchars($item->content ?? '') ?></textarea>
                    <input type="hidden" name="items[tour_overview][<?= (int)$index ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">

                    <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                    <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image mt-2">Upload</button>
                    <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                </div>
            <?php endforeach; ?>

            <h2>Discover Section</h2>
            <div class="mb-3">
                <label for="discover_title" class="form-label">Title</label>
                <input id="discover_title" name="sections[discover][title]" class="form-control" rows="2" value="<?= htmlspecialchars($discover?->title) ?>">
            </div>
            <div class="mb-3">
                <label for="discover_subtitle" class="form-label">Subtitle</label>
                <textarea id="discover_subtitle" name="sections[discover][subtitle]" data-quill="1" class="form-control" rows="2"><?= htmlspecialchars($discover?->subTitle) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="discover_description" class="form-label">Description</label>
                <textarea id="discover_description" name="sections[discover][description]" data-quill="1" class="form-control" rows="4"><?= htmlspecialchars($discover?->description) ?></textarea>
            </div>

            <?php foreach ($discoverGridItems as $index => $item): ?>
                <input type="hidden" name="items[discover][<?= (int)$index ?>][id]" value="<?= (int)$item->id ?>">
                <div class="mb-3">
                    <label class="form-label"><?= htmlspecialchars($item->title ?? '') ?> Icon</label>
                    <input type="text" name="items[discover][<?= (int)$index ?>][icon_class]" class="form-control" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                    <label class="form-label"><?= htmlspecialchars($item->title ?? '') ?> Title</label>
                    <input type="text" name="items[discover][<?= (int)$index ?>][title]" class="form-control" value="<?= htmlspecialchars($item->title) ?>">
                    <label class="form-label"><?= htmlspecialchars($item->title ?? '') ?> Content</label>
                    <textarea name="items[discover][<?= (int)$index ?>][content]" data-quill="1" class="form-control" rows="2"><?= htmlspecialchars($item->content) ?></textarea>
                </div>
                <input type="hidden" name="items[discover][<?= (int)$index ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                <input type="hidden" name="items[discover][<?= (int)$index ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                <input type="hidden" name="items[discover][<?= (int)$index ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
            <?php endforeach; ?>

            <?php foreach ($discoverPriceItems as $index => $item): ?>
                <?php $rowIndex = $index + 100; ?>
                <input type="hidden" name="items[discover][<?= (int)$rowIndex ?>][id]" value="<?= (int)$item->id ?>">

                <div class="mb-3">
                    <label class="form-label"><?= htmlspecialchars($item->title ?? '') ?> Title</label>
                    <input type="text" name="items[discover][<?= (int)$rowIndex ?>][title]" class="form-control" value="<?= htmlspecialchars($item->title) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Price Item <?= (int)$index + 1 ?> Link</label>
                    <input type="text" name="items[discover][<?= (int)$rowIndex ?>][link_url]" class="form-control" value="<?= htmlspecialchars($item->url) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Price Item <?= (int)$index + 1 ?> Content</label>
                    <textarea name="items[discover][<?= (int)$rowIndex ?>][content]" data-quill="1" class="form-control" rows="2"><?= htmlspecialchars($item->content) ?></textarea>
                </div>
                <input type="hidden" name="items[discover][<?= (int)$rowIndex ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                <input type="hidden" name="items[discover][<?= (int)$rowIndex ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                <input type="hidden" name="items[discover][<?= (int)$rowIndex ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
            <?php endforeach; ?>

            <?php foreach ($discoverInfoItems as $index => $item): ?>
                <?php $rowIndex = $index + 200; ?>
                <input type="hidden" name="items[discover][<?= (int)$rowIndex ?>][id]" value="<?= (int)$item->id ?>">

                <div class="mb-3">
                    <label class="form-label">Info Item <?= (int)$index + 1 ?> Title</label>
                    <input type="text" name="items[discover][<?= (int)$rowIndex ?>][title]" class="form-control" value="<?= htmlspecialchars($item->title) ?>">
                </div>
                <input type="hidden" name="items[discover][<?= (int)$rowIndex ?>][content]" value="<?= htmlspecialchars($item->content ?? '') ?>">
                <input type="hidden" name="items[discover][<?= (int)$rowIndex ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                <input type="hidden" name="items[discover][<?= (int)$rowIndex ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '')?>">
                <input type="hidden" name="items[discover][<?= (int)$rowIndex ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                <input type="hidden" name="items[discover][<?= (int)$rowIndex ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
            <?php endforeach; ?>

            <h2>Meet your Guides Section</h2>
            <div class="mb-3">
                <label for="guide_title" class="form-label">Title</label>
                <input id="guide_title" name="sections[guide][title]" class="form-control" rows="2" value="<?= htmlspecialchars($guide?->title) ?>">
            </div>
            <div class="mb-3">
                <label for="guide_subtitle" class="form-label">SubTitle</label>
                <input id="guide_subtitle" name="sections[guide][subtitle]" class="form-control" rows="2" value="<?= htmlspecialchars($guide?->subTitle) ?>">
            </div>
            <div class="mb-3">
                <label for="guide_description" class="form-label">Description</label>
                <textarea id="guide_description" name="sections[guide][description]" data-quill="1" class="form-control" rows="2"><?= htmlspecialchars($guide?->description) ?></textarea>
            </div>

            <?php foreach ($guideItems as $index => $item): ?>
                <div class="mb-3" data-tour-upload-row="1" data-tour-section-type="guide" data-tour-item-category="<?= htmlspecialchars((string)($item->category ?? '')) ?>">
                    <input type="hidden" name="items[guide][<?= (int)$index ?>][id]" class="tour-item-id" value="<?= (int)$item->id ?>">
                    <input type="hidden" name="items[guide][<?= (int)$index ?>][image_path]" class="performer-artist-image" value="<?= htmlspecialchars($item->image ?? '') ?>">

                    <input type="file" class="form-control form-control-sm performer-upload-input mb-2" accept="image/jpeg,image/png,image/webp">
                    <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image mb-2">Upload</button>
                    <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>

                    <label class="form-label mt-2">Guide <?= (int)$index + 1 ?> Name</label>
                    <input type="text" name="items[guide][<?= (int)$index ?>][title]" class="form-control mb-2" value="<?= htmlspecialchars($item->title) ?>">

                    <label class="form-label">Guide <?= (int)$index + 1 ?> Role</label>
                    <input type="text" name="items[guide][<?= (int)$index ?>][item_subtitle]" class="form-control mb-2" value="<?= htmlspecialchars($item->subTitle) ?>">

                    <label class="form-label">Guide <?= (int)$index + 1 ?> Description</label>
                    <textarea name="items[guide][<?= (int)$index ?>][content]" data-quill="1" class="form-control" rows="3"><?= htmlspecialchars($item->content) ?></textarea>
                    <input type="hidden" name="items[guide][<?= (int)$index ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                    <input type="hidden" name="items[guide][<?= (int)$index ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                    <input type="hidden" name="items[guide][<?= (int)$index ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary save-btn">Save Changes</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../partialsViews/cms/upload-feedback-modal.php'; ?>

<script src="/js/cms/upload-feedback.js"></script>
<script src="/js/cms/media-upload.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script src="/js/cms/page-editor.js"></script>
<script src="/js/cms/tour-home.js"></script>
