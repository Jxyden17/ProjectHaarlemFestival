<?php
$heroImageItems = $hero?->getItemsByCategorie('image') ?? [];
$heroTagItems = $hero?->getItemsByCategorie('tag') ?? [];
$aboutItems = $about?->getItemsByCategorie('paragraph') ?? [];
$galleryItems = $gallery?->getItemsByCategorie('gallery') ?? [];
$featuredItems = $featured?->items ?? [];
$bookingButtonItems = $booking?->getItemsByCategorie('button') ?? [];
$bookingPriceItems = $booking?->getItemsByCategorie('price') ?? [];
$bookingPriceLabelItems = $booking?->getItemsByCategorie('price_label') ?? [];
$bookingDateItems = $booking?->getItemsByCategorie('datetime') ?? [];
$bookingLocationItems = $booking?->getItemsByCategorie('location') ?? [];
$bookingTagItems = $booking?->getItemsByCategorie('tag') ?? [];
?>

<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Stories Detail Content</h1>
        <a href="/cms/events" class="btn btn-outline-secondary">Back to Events</a>
    </div>

    <?php
    $successMessage = 'Stories detail content updated.';
    include __DIR__ . '/../../partialsViews/cms/form-feedback.php';
    ?>

    <form method="POST" action="/cms/events/stories-details" class="card" data-quill-form="1">
        <div class="card-body">
            <input type="hidden" name="page_id" value="<?= (int)($pageId ?? 0) ?>">

            <h2 class="h4">Hero</h2>
            <div class="mb-3">
                <label class="form-label">Title</label>
                <textarea name="sections[hero][title]" class="form-control" data-quill="1" rows="2"><?= htmlspecialchars($hero?->title ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Subtitle</label>
                <textarea name="sections[hero][subtitle]" class="form-control" data-quill="1" rows="2"><?= htmlspecialchars($hero?->subTitle ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="sections[hero][description]" class="form-control" data-quill="1" rows="3"><?= htmlspecialchars($hero?->description ?? '') ?></textarea>
            </div>

            <?php foreach ($heroImageItems as $index => $item): ?>
                <div class="mb-3 border rounded p-3">
                    <input type="hidden" name="items[hero][<?= (int)$index ?>][id]" value="<?= (int)$item->id ?>">
                    <input type="hidden" name="items[hero][<?= (int)$index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                    <label class="form-label">Hero Image Title</label>
                    <textarea name="items[hero][<?= (int)$index ?>][title]" class="form-control mb-2" rows="2"><?= htmlspecialchars($item->title ?? '') ?></textarea>
                    <label class="form-label">Hero Image Path</label>
                    <input type="text" name="items[hero][<?= (int)$index ?>][image_path]" class="form-control mb-2" value="<?= htmlspecialchars($item->image ?? '') ?>">
                    <input type="hidden" name="items[hero][<?= (int)$index ?>][content]" value="<?= htmlspecialchars($item->content ?? '') ?>">
                    <input type="hidden" name="items[hero][<?= (int)$index ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                    <input type="hidden" name="items[hero][<?= (int)$index ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                    <input type="hidden" name="items[hero][<?= (int)$index ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                    <input type="hidden" name="items[hero][<?= (int)$index ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
                </div>
            <?php endforeach; ?>

            <?php foreach ($heroTagItems as $index => $item): ?>
                <?php $rowIndex = $index + 100; ?>
                <div class="mb-3">
                    <input type="hidden" name="items[hero][<?= (int)$rowIndex ?>][id]" value="<?= (int)$item->id ?>">
                    <input type="hidden" name="items[hero][<?= (int)$rowIndex ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                    <label class="form-label">Hero Tag <?= (int)$index + 1 ?></label>
                    <input type="text" name="items[hero][<?= (int)$rowIndex ?>][title]" class="form-control" value="<?= htmlspecialchars($item->title ?? '') ?>">
                    <input type="hidden" name="items[hero][<?= (int)$rowIndex ?>][content]" value="<?= htmlspecialchars($item->content ?? '') ?>">
                    <input type="hidden" name="items[hero][<?= (int)$rowIndex ?>][image_path]" value="<?= htmlspecialchars($item->image ?? '') ?>">
                    <input type="hidden" name="items[hero][<?= (int)$rowIndex ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                    <input type="hidden" name="items[hero][<?= (int)$rowIndex ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                    <input type="hidden" name="items[hero][<?= (int)$rowIndex ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                    <input type="hidden" name="items[hero][<?= (int)$rowIndex ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
                </div>
            <?php endforeach; ?>

            <h2 class="h4 mt-4">About</h2>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <textarea name="sections[about][title]" class="form-control" data-quill="1" rows="2"><?= htmlspecialchars($about?->title ?? '') ?></textarea>
                <input type="hidden" name="sections[about][subtitle]" value="<?= htmlspecialchars($about?->subTitle ?? '') ?>">
                <input type="hidden" name="sections[about][description]" value="<?= htmlspecialchars($about?->description ?? '') ?>">
            </div>
            <?php foreach ($aboutItems as $index => $item): ?>
                <div class="mb-3">
                    <input type="hidden" name="items[about][<?= (int)$index ?>][id]" value="<?= (int)$item->id ?>">
                    <input type="hidden" name="items[about][<?= (int)$index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                    <label class="form-label">Paragraph <?= (int)$index + 1 ?></label>
                    <textarea name="items[about][<?= (int)$index ?>][content]" class="form-control" data-quill="1" rows="4"><?= htmlspecialchars($item->content ?? '') ?></textarea>
                    <input type="hidden" name="items[about][<?= (int)$index ?>][title]" value="<?= htmlspecialchars($item->title ?? '') ?>">
                    <input type="hidden" name="items[about][<?= (int)$index ?>][image_path]" value="<?= htmlspecialchars($item->image ?? '') ?>">
                    <input type="hidden" name="items[about][<?= (int)$index ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                    <input type="hidden" name="items[about][<?= (int)$index ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                    <input type="hidden" name="items[about][<?= (int)$index ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                    <input type="hidden" name="items[about][<?= (int)$index ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
                </div>
            <?php endforeach; ?>

            <h2 class="h4 mt-4">Gallery</h2>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <textarea name="sections[gallery][title]" class="form-control" data-quill="1" rows="2"><?= htmlspecialchars($gallery?->title ?? '') ?></textarea>
                <input type="hidden" name="sections[gallery][subtitle]" value="<?= htmlspecialchars($gallery?->subTitle ?? '') ?>">
                <input type="hidden" name="sections[gallery][description]" value="<?= htmlspecialchars($gallery?->description ?? '') ?>">
            </div>
            <?php foreach ($galleryItems as $index => $item): ?>
                <div class="mb-3 border rounded p-3">
                    <input type="hidden" name="items[gallery][<?= (int)$index ?>][id]" value="<?= (int)$item->id ?>">
                    <input type="hidden" name="items[gallery][<?= (int)$index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                    <label class="form-label">Image Label</label>
                    <input type="text" name="items[gallery][<?= (int)$index ?>][title]" class="form-control mb-2" value="<?= htmlspecialchars($item->title ?? '') ?>">
                    <label class="form-label">Image Path</label>
                    <input type="text" name="items[gallery][<?= (int)$index ?>][image_path]" class="form-control" value="<?= htmlspecialchars($item->image ?? '') ?>">
                    <input type="hidden" name="items[gallery][<?= (int)$index ?>][content]" value="<?= htmlspecialchars($item->content ?? '') ?>">
                    <input type="hidden" name="items[gallery][<?= (int)$index ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                    <input type="hidden" name="items[gallery][<?= (int)$index ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                    <input type="hidden" name="items[gallery][<?= (int)$index ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                    <input type="hidden" name="items[gallery][<?= (int)$index ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
                </div>
            <?php endforeach; ?>

            <h2 class="h4 mt-4">Featured</h2>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <textarea name="sections[featured][title]" class="form-control" data-quill="1" rows="2"><?= htmlspecialchars($featured?->title ?? '') ?></textarea>
                <input type="hidden" name="sections[featured][subtitle]" value="<?= htmlspecialchars($featured?->subTitle ?? '') ?>">
                <input type="hidden" name="sections[featured][description]" value="<?= htmlspecialchars($featured?->description ?? '') ?>">
            </div>
            <?php foreach ($featuredItems as $index => $item): ?>
                <div class="mb-3">
                    <input type="hidden" name="items[featured][<?= (int)$index ?>][id]" value="<?= (int)$item->id ?>">
                    <input type="hidden" name="items[featured][<?= (int)$index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                    <label class="form-label">Feature Label</label>
                    <input type="text" name="items[featured][<?= (int)$index ?>][title]" class="form-control mb-2" value="<?= htmlspecialchars($item->title ?? '') ?>">
                    <label class="form-label">Feature Link</label>
                    <input type="text" name="items[featured][<?= (int)$index ?>][link_url]" class="form-control mb-2" value="<?= htmlspecialchars($item->url ?? '') ?>">
                    <input type="hidden" name="items[featured][<?= (int)$index ?>][content]" value="<?= htmlspecialchars($item->content ?? '') ?>">
                    <input type="hidden" name="items[featured][<?= (int)$index ?>][image_path]" value="<?= htmlspecialchars($item->image ?? '') ?>">
                    <input type="hidden" name="items[featured][<?= (int)$index ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                    <input type="hidden" name="items[featured][<?= (int)$index ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                    <input type="hidden" name="items[featured][<?= (int)$index ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
                </div>
            <?php endforeach; ?>

            <h2 class="h4 mt-4">Booking</h2>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <textarea name="sections[booking][title]" class="form-control" data-quill="1" rows="2"><?= htmlspecialchars($booking?->title ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Section Subtitle</label>
                <textarea name="sections[booking][subtitle]" class="form-control" data-quill="1" rows="2"><?= htmlspecialchars($booking?->subTitle ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Section Description</label>
                <textarea name="sections[booking][description]" class="form-control" data-quill="1" rows="3"><?= htmlspecialchars($booking?->description ?? '') ?></textarea>
            </div>

            <?php foreach ([$bookingPriceItems, $bookingPriceLabelItems, $bookingDateItems, $bookingLocationItems, $bookingButtonItems, $bookingTagItems] as $group): ?>
                <?php foreach ($group as $item): ?>
                    <div class="mb-3">
                        <input type="hidden" name="items[booking][<?= (int)$item->id ?>][id]" value="<?= (int)$item->id ?>">
                        <input type="hidden" name="items[booking][<?= (int)$item->id ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                        <label class="form-label"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $item->category ?? 'item'))) ?></label>
                        <input type="text" name="items[booking][<?= (int)$item->id ?>][title]" class="form-control mb-2" value="<?= htmlspecialchars($item->title ?? '') ?>">
                        <label class="form-label">Link / Extra Value</label>
                        <input type="text" name="items[booking][<?= (int)$item->id ?>][link_url]" class="form-control mb-2" value="<?= htmlspecialchars($item->url ?? '') ?>">
                        <input type="hidden" name="items[booking][<?= (int)$item->id ?>][content]" value="<?= htmlspecialchars($item->content ?? '') ?>">
                        <input type="hidden" name="items[booking][<?= (int)$item->id ?>][image_path]" value="<?= htmlspecialchars($item->image ?? '') ?>">
                        <input type="hidden" name="items[booking][<?= (int)$item->id ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                        <input type="hidden" name="items[booking][<?= (int)$item->id ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                        <input type="hidden" name="items[booking][<?= (int)$item->id ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<?php $pageEditorJsVersion = @filemtime(__DIR__ . '/../../../../public/js/cms/page-editor.js') ?: time(); ?>
<script src="/js/cms/page-editor.js?v=<?= (int)$pageEditorJsVersion ?>"></script>
