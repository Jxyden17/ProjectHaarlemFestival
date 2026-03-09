<?php
$heroItems = $hero?->items ?? [];
$stopsItems = $stops?->items ?? [];
$discoverGridItems = $discover?->getItemsByCategorie('grid') ?? [];
$discoverPriceItems = $discover?->getItemsByCategorie('price') ?? [];
$discoverInfoItems = $discover?->getItemsByCategorie('info') ?? [];
$guideItems = $guide?->getItemsByCategorie('guide') ?? [];
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


<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

    <form method="POST" action="/cms/events/tour-home" class="card">
        <div class="card-body">
            <h2 class="h2">Banner</h2>
            <div class="mb-3">
                <label for="hero_title" class="form-label">Title</label>
                <textarea id="hero_title" name="sections[hero][title]" class="form-control" data-quill="1" rows="2" required><?= htmlspecialchars($hero->title ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label for="hero_subtitle" class="form-label">SubTitle</label>
                <textarea id="hero_subtitle" name="sections[hero][subtitle]" class="form-control" data-quill="1" rows="2" required><?= htmlspecialchars($hero->subTitle ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Fotos</label>
                <?php foreach ($heroItems as $index => $item): ?>
                    <div class="d-flex flex-wrap gap-2 align-items-center performer-image-row mb-2">
                        <input type="hidden" name="items[hero][<?= (int)$index ?>][id]" value="<?= (int)$item->id ?>">
                        <input type="hidden" name="items[hero][<?= (int)$index ?>][order_index]" value="<?= (int)($item->position ?? ($index + 1)) ?>">
                        <input type="hidden" name="items[hero][<?= (int)$index ?>][item_category]" value="hero">
                        <input type="hidden" name="items[hero][<?= (int)$index ?>][image_path]" class="performer-image-path" value="<?= htmlspecialchars($item->image ?? '') ?>">

                        <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                        <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                        <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                    </div>
                <?php endforeach; ?>
            </div>

            <h2>Stops on the tour</h2>
            <div class="mb-3">
                <label for="tour_overview_title"  class="form-label">Title</label>
                <textarea id="tour_overview_title" name="sections[tour_overview][title]" data-quill="1" class="form-control" rows="2" required><?= htmlspecialchars($stops?->title ?? '') ?></textarea>
            </div>

            <?php foreach ($stopsItems as $index => $item): ?>
                <div class="mb-3">

                    <label class="form-label">Title</label>
                    <textarea name="items[tour_overview][<?= (int)$index ?>][title]" data-quill="1" class="form-control mb-2" rows="2" required><?= htmlspecialchars($item->title ?? '') ?></textarea>

                    <label class="form-label">Subtitle</label>
                    <textarea name="items[tour_overview][<?= (int)$index ?>][item_subtitle]" data-quill="1" class="form-control mb-2" rows="2"><?= htmlspecialchars($item->subTitle ?? '') ?></textarea>

                    <label class="form-label">Duration</label>
                    <textarea name="items[tour_overview][<?= (int)$index ?>][duration]" data-quill="1" class="form-control mb-2" rows="2"><?= htmlspecialchars($item->duration ?? '') ?></textarea>

                    <label class="form-label">Content</label>
                    <textarea name="items[tour_overview][<?= (int)$index ?>][content]" data-quill="1" class="form-control mb-2" rows="3"><?= htmlspecialchars($item->content ?? '') ?></textarea>

                    <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                    <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image mt-2">Upload</button>
                    <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                </div>
            <?php endforeach; ?>

            <h2>Discover</h2>
            <div class="mb-3">
                <label for="discover_title" class="form-label">Title</label>
                <textarea id="discover_title" name="sections[discover][title]" data-quill="1" class="form-control" rows="2" required><?= htmlspecialchars($discover?->title ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="discover_subtitle" class="form-label">Subtitle</label>
                <textarea id="discover_subtitle" name="sections[discover][subtitle]" data-quill="1" class="form-control" rows="2"><?= htmlspecialchars($discover?->subTitle ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="discover_description" class="form-label">Description</label>
                <textarea id="discover_description" name="sections[discover][description]" data-quill="1" class="form-control" rows="4"><?= htmlspecialchars($discover?->description ?? '') ?></textarea>
            </div>

            <?php foreach ($discoverGridItems as $index => $item): ?>

                <div class="mb-3">
                    <label class="form-label">Grid Item <?= (int)$index + 1 ?> Title</label>
                    <textarea name="items[discover][<?= (int)$index ?>][title]" data-quill="1" class="form-control" rows="2"><?= htmlspecialchars($item->title ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Grid Item <?= (int)$index + 1 ?> Content</label>
                    <textarea name="items[discover][<?= (int)$index ?>][content]" data-quill="1" class="form-control" rows="2"><?= htmlspecialchars($item->content ?? '') ?></textarea>
                </div>
            <?php endforeach; ?>

            <?php foreach ($discoverPriceItems as $index => $item): ?>
                <?php $rowIndex = $index + 100; ?>
                <input type="hidden" name="items[discover][<?= (int)$rowIndex ?>][id]" value="<?= (int)$item->id ?>">
                <input type="hidden" name="items[discover][<?= (int)$rowIndex ?>][order_index]" value="<?= (int)($item->position ?? ($index + 1)) ?>">
                <input type="hidden" name="items[discover][<?= (int)$rowIndex ?>][item_category]" value="price">

                <div class="mb-3">
                    <label class="form-label">Price Item <?= (int)$index + 1 ?> Title</label>
                    <textarea name="items[discover][<?= (int)$rowIndex ?>][title]" data-quill="1" class="form-control" rows="2"><?= htmlspecialchars($item->title ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Price Item <?= (int)$index + 1 ?> Link</label>
                    <textarea name="items[discover][<?= (int)$rowIndex ?>][link_url]" data-quill="1" class="form-control" rows="2"><?= htmlspecialchars($item->url ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Price Item <?= (int)$index + 1 ?> Content</label>
                    <textarea name="items[discover][<?= (int)$rowIndex ?>][content]" data-quill="1" class="form-control" rows="2"><?= htmlspecialchars($item->content ?? '') ?></textarea>
                </div>
            <?php endforeach; ?>

            <?php foreach ($discoverInfoItems as $index => $item): ?>
                <?php $rowIndex = $index + 200; ?>
                <input type="hidden" name="items[discover][<?= (int)$rowIndex ?>][id]" value="<?= (int)$item->id ?>">
                <input type="hidden" name="items[discover][<?= (int)$rowIndex ?>][order_index]" value="<?= (int)($item->position ?? ($index + 1)) ?>">
                <input type="hidden" name="items[discover][<?= (int)$rowIndex ?>][item_category]" value="info">

                <div class="mb-3">
                    <label class="form-label">Info Item <?= (int)$index + 1 ?> Title</label>
                    <textarea name="items[discover][<?= (int)$rowIndex ?>][title]" data-quill="1" class="form-control" rows="2"><?= htmlspecialchars($item->title ?? '') ?></textarea>
                </div>
            <?php endforeach; ?>

            <h2>Meet your Guides</h2>
            <div class="mb-3">
                <label for="guide_title" class="form-label">Title</label>
                <textarea id="guide_title" name="sections[guide][title]" data-quill="1" class="form-control" rows="2"><?= htmlspecialchars($guide?->title ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="guide_subtitle" class="form-label">SubTitle</label>
                <textarea id="guide_subtitle" name="sections[guide][subtitle]" data-quill="1" class="form-control" rows="2"><?= htmlspecialchars($guide?->subTitle ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="guide_description" class="form-label">Description</label>
                <textarea id="guide_description" name="sections[guide][description]" data-quill="1" class="form-control" rows="2"><?= htmlspecialchars($guide?->description ?? '') ?></textarea>
            </div>

            <?php foreach ($guideItems as $index => $item): ?>
                <div class="mb-3">
                    <input type="hidden" name="items[guide][<?= (int)$index ?>][id]" value="<?= (int)$item->id ?>">
                    <input type="hidden" name="items[guide][<?= (int)$index ?>][order_index]" value="<?= (int)($item->position ?? ($index + 1)) ?>">
                    <input type="hidden" name="items[guide][<?= (int)$index ?>][item_category]" value="guide">
                    <input type="hidden" name="items[guide][<?= (int)$index ?>][image_path]" class="performer-image-path" value="<?= htmlspecialchars($item->image ?? '') ?>">

                    <input type="file" class="form-control form-control-sm performer-upload-input mb-2" accept="image/jpeg,image/png,image/webp">
                    <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image mb-2">Upload</button>
                    <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>

                    <label class="form-label mt-2">Guide <?= (int)$index + 1 ?> Name</label>
                    <textarea name="items[guide][<?= (int)$index ?>][title]" data-quill="1" class="form-control mb-2" rows="2"><?= htmlspecialchars($item->title ?? '') ?></textarea>

                    <label class="form-label">Guide <?= (int)$index + 1 ?> Role</label>
                    <textarea name="items[guide][<?= (int)$index ?>][item_subtitle]" data-quill="1" class="form-control mb-2" rows="2"><?= htmlspecialchars($item->subTitle ?? '') ?></textarea>

                    <label class="form-label">Guide <?= (int)$index + 1 ?> Description</label>
                    <textarea name="items[guide][<?= (int)$index ?>][content]" data-quill="1" class="form-control" rows="3"><?= htmlspecialchars($item->content ?? '') ?></textarea>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<?php $tourHomeJsVersion = @filemtime(__DIR__ . '/../../../../public/js/cms/tour-home.js') ?: time(); ?>
<script src="/js/cms/tour-home.js?v=<?= (int)$tourHomeJsVersion ?>"></script>
