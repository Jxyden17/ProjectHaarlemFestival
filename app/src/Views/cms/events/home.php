<?php
$heroItems = $hero?->items ?? [];
$aboutItems = $about?->items ?? [];
$labels = $about?->getItemsByCategorie('label') ?? [];
$discoverItems = $discover?->getItemsByCategorie('event_card') ?? [];
$faqItems = $faq?->getItemsByCategorie('faq_item') ?? [];
$mapItems = $map?->getItemsByCategorie('map_location') ?? [];
?>

<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Home Page Content</h1>
        <a href="/cms/events" class="btn btn-outline-secondary">Back to Events</a>
    </div>

    <?php
    $successMessage = 'Home page content updated.';
    include __DIR__ . '/../../partialsViews/cms/form-feedback.php';
    ?>

    <form method="POST" action="/cms/events/home" class="card">
        <div class="card-body">
            <h2 class="h5">Hero Section</h2>
            <input type="hidden" name="sections[hero][title]" value="<?= htmlspecialchars($hero?->title ?? '') ?>">
            <input type="hidden" name="sections[hero][subtitle]" value="<?= htmlspecialchars($hero?->subTitle ?? '') ?>">
            <input type="hidden" name="sections[hero][description]" value="<?= htmlspecialchars($hero?->description ?? '') ?>">
            <?php foreach ($heroItems as $index => $item): ?>
                <div class="mb-3">
                    <input type="hidden" name="items[hero][<?= (int)$index ?>][id]" value="<?= (int)$item->id ?>">
                    <label class="form-label">Foto</label>
                    <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                    <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                    <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                    <label class="form-label">Title</label>
                    <input type="text" name="items[hero][<?= (int)$index ?>][title]" class="form-control mb-2" value="<?= htmlspecialchars($item->title ?? '') ?>">
                    <label class="form-label">Date</label>
                    <input type="text" name="items[hero][<?= (int)$index ?>][content]" class="form-control mb-2" value="<?= htmlspecialchars($item->content ?? '') ?>">
                    <label class="form-label">SubTitle</label>
                    <input type="text" name="items[hero][<?= (int)$index ?>][subTitle]" class="form-control mb-2" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
                </div>
            <?php endforeach; ?>

            <h2 class="h5 mt-4">About Section</h2>
            <label class="form-label">Title</label>
            <input type="text" name="sections[about][title]" class="form-control mb-2" value="<?= htmlspecialchars($about?->title ?? '') ?>">
            <label class="form-label">SubTitle</label>
            <input type="text" name="sections[about][subtitle]" class="form-control mb-2" value="<?= htmlspecialchars($about?->subTitle ?? '') ?>">
            <label class="form-label">Description</label>
            <input type="text" name="sections[about][description]" class="form-control mb-2" value="<?= htmlspecialchars($about?->description ?? '') ?>">
            <div class="mb-3">
                <label class="form-label">Labels</label>
                <?php foreach ($labels as $index => $label): ?>
                    <div class="d-flex gap-2 mb-2">
                        <input type="hidden" name="items[about][<?= (int)$index ?>][id]" value="<?= (int)$label->id ?>">
                        <input type="text" name="items[about][<?= (int)$index ?>][icon]" class="form-control" placeholder="Icon" value="<?= htmlspecialchars($label->icon ?? $label->subTitle ?? '') ?>">
                        <input type="text" name="items[about][<?= (int)$index ?>][title]" class="form-control" placeholder="Title" value="<?= htmlspecialchars($label->title ?? '') ?>">
                    </div>
                <?php endforeach; ?>
            </div>

            <h2 class="h5 mt-4">Discover Events</h2>
            <label class="form-label">Section Title</label>
            <input type="text" name="sections[discover_events][title]" class="form-control mb-2" value="<?= htmlspecialchars($discover?->title ?? '') ?>">
            <?php foreach ($discoverItems as $index => $item): ?>
                <div class="mb-3">
                    <input type="hidden" name="items[discover_events][<?= (int)$index ?>][id]" value="<?= (int)$item->id ?>">
                    <label class="form-label">Foto</label>
                    <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                    <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                    <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                    <label class="form-label">Title</label>
                    <input type="text" name="items[discover_events][<?= (int)$index ?>][title]" class="form-control mb-2" value="<?= htmlspecialchars($item->title ?? '') ?>">
                    <label class="form-label">Content</label>
                    <textarea name="items[discover_events][<?= (int)$index ?>][content]" class="form-control mb-2" rows="2"><?= htmlspecialchars($item->content ?? '') ?></textarea>
                </div>
            <?php endforeach; ?>

            <h2 class="h5 mt-4">Map Section</h2>
            <label class="form-label">Section Title</label>
            <input type="text" name="sections[map_section][title]" class="form-control mb-2" value="<?= htmlspecialchars($map?->title ?? '') ?>">
            <?php foreach ($mapItems as $index => $item): ?>
                <div class="mb-3">
                    <input type="hidden" name="items[map_section][<?= (int)$index ?>][id]" value="<?= (int)$item->id ?>">
                    <label class="form-label">Location Title</label>
                    <input type="text" name="items[map_section][<?= (int)$index ?>][title]" class="form-control mb-2" value="<?= htmlspecialchars($item->title ?? '') ?>">
                    <label class="form-label">Location Content</label>
                    <input type="text" name="items[map_section][<?= (int)$index ?>][content]" class="form-control mb-2" value="<?= htmlspecialchars($item->content ?? '') ?>">
                </div>
            <?php endforeach; ?>

            <h2 class="h5 mt-4">FAQ Section</h2>
            <label class="form-label">Section Title</label>
            <input type="text" name="sections[faq][title]" class="form-control mb-2" value="<?= htmlspecialchars($faq?->title ?? '') ?>">
            <?php foreach ($faqItems as $index => $item): ?>
                <div class="mb-3">
                    <input type="hidden" name="items[faq][<?= (int)$index ?>][id]" value="<?= (int)$item->id ?>">
                    <label class="form-label">Question</label>
                    <input type="text" name="items[faq][<?= (int)$index ?>][title]" class="form-control mb-2" value="<?= htmlspecialchars($item->title ?? '') ?>">
                    <label class="form-label">Answer</label>
                    <textarea name="items[faq][<?= (int)$index ?>][content]" class="form-control mb-2" rows="2"><?= htmlspecialchars($item->content ?? '') ?></textarea>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary save-btn">Save Changes</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<?php $tourDetailsJsVersion = @filemtime(__DIR__ . '/../../../../public/js/cms/tour-details.js') ?: time(); ?>
<script src="/js/cms/tour-details.js?v=<?= (int)$tourDetailsJsVersion ?>"></script>