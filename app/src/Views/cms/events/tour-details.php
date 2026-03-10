<?php
$heroItems = $header?->items ?? [];
$historyItems = $history?->items ?? [];
$didYouKnowItems = $didYouKnow?->items ?? [];
$openingTimeInfo = $openingTime->getItemsByCategorie('info') ?? [];
$openingTimeHours = $openingTime->getItemsByCategorie('opening_hours') ?? [];
?>

<link rel="stylesheet" href="/css/Cms/cms-layout.css">
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Tour Detail Content</h1>
        <a href="/cms/events" class="btn btn-outline-secondary">Back to Events</a>
    </div>

    <?php
    $successMessage = 'Tour detail content updated.';
    include __DIR__ . '/../../partialsViews/cms/form-feedback.php';
    ?>

    <form method="POST" action="/cms/events/tour-details" class="card">
        <div class="card-body">
        <input type="hidden" name="page_id" value="<?= (int)($pageId ?? 0) ?>">
             <h2 class="h2">Banner</h2>
            <div class="mb-3">
                <label for="hero_title" class="form-label">Title</label>
                <textarea id="hero_title" name="sections[hero][title]" class="form-control" data-quill="1" rows="2" required><?= htmlspecialchars($header->title ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label for="hero_subtitle" class="form-label">SubTitle</label>
                <textarea id="hero_subtitle" name="sections[hero][subtitle]" class="form-control" data-quill="1" rows="2" required><?= htmlspecialchars($header->subTitle ?? '') ?></textarea>
            </div>

             <div class="mb-3">
                <label class="form-label">Fotos</label>
                <?php foreach ($heroItems as $index => $item): ?>
                    <div class="d-flex flex-wrap gap-2 align-items-center performer-image-row mb-2">
                        <input type="hidden" name="items[hero][<?= (int)$index ?>][id]" value="<?= (int)$item->id ?>">
                        <input type="hidden" name="items[hero][<?= (int)$index ?>][image_path]" class="performer-image-path" value="<?= htmlspecialchars($item->image ?? '') ?>">
                        <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                        <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                        <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <h2 class="h5 mt-4">History</h2>
            <input type="hidden" name="sections[history][order_index]" value="<?= (int)($history->orderIndex ?? 2) ?>">
            <textarea name="sections[history][title]" data-quill="1" class="form-control mb-2" rows="2"><?= htmlspecialchars($history->title ?? '') ?></textarea>
            <?php foreach ($historyItems as $index => $item): ?>
                <div class="mb-3">
                    <input type="hidden" name="items[history][<?= (int)$index ?>][id]" value="<?= (int)$item->id ?>">
                    <input type="hidden" name="items[history][<?= (int)$index ?>][image_path]" value="<?= htmlspecialchars($item->image ?? '') ?>">
                    <input type="hidden" name="items[history][<?= (int)$index ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                    <textarea name="items[history][<?= (int)$index ?>][title]" data-quill="1" class="form-control mb-2" rows="2"><?= htmlspecialchars($item->title ?? '') ?></textarea>
                    <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                        <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                        <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                    <textarea name="items[history][<?= (int)$index ?>][content]" data-quill="1" class="form-control mb-2" rows="2"><?= htmlspecialchars($item->content ?? '') ?></textarea>
                </div>
            <?php endforeach; ?>

            <h2 class="h5 mt-4">Did You Know</h2>
            <input type="hidden" name="sections[did_you_know][order_index]" value="<?= (int)($didYouKnow->orderIndex ?? 3) ?>">
            <textarea name="sections[did_you_know][title]" data-quill="1" class="form-control mb-3" rows="3"><?= htmlspecialchars($didYouKnow->title ?? '') ?></textarea>
            <?php foreach ($didYouKnowItems as $index => $item): ?>
                <div class="mb-3">
                    <input type="hidden" name="items[did_you_know][<?= (int)$index ?>][id]" value="<?= (int)$item->id ?>">
                    <input type="hidden" name="items[did_you_know][<?= (int)$index ?>][image_path]" value="<?= htmlspecialchars($item->image ?? '') ?>">
                    <input type="hidden" name="items[did_you_know][<?= (int)$index ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                    <textarea name="items[did_you_know][<?= (int)$index ?>][title]" data-quill="1" class="form-control mb-2" rows="2"><?= htmlspecialchars($item->title ?? '') ?></textarea>
                    <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                        <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                        <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                    <textarea name="items[did_you_know][<?= (int)$index ?>][content]" data-quill="1" class="form-control mb-2" rows="2"><?= htmlspecialchars($item->content ?? '') ?></textarea>
                </div>
            <?php endforeach; ?>


            <h2 class="h5 mt-4">MapSections</h2>
            <input type="hidden" name="sections[openingTime][order_index]" value="<?= (int)($openingTime->orderIndex ?? 4) ?>">
            <textarea name="sections[openingTime][title]" data-quill="1" class="form-control mb-2" rows="2"><?= htmlspecialchars($openingTime->title ?? '') ?></textarea>
            <textarea name="sections[openingTime][description]" data-quill="1" class="form-control mb-3" rows="3"><?= htmlspecialchars($openingTime->description ?? '') ?></textarea>
            <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                        <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                        <a href="<?= htmlspecialchars($openingTime->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($openingTime->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
            <h2 class="h5 mt-4">Informations</h2>
            <?php foreach ($openingTimeInfo as $index => $item): ?>
                <div class="mb-3">
                    <input type="hidden" name="items[openingTime][<?= (int)$index ?>][id]" value="<?= (int)$item->id ?>">
                    <textarea name="items[openingTime][<?= (int)$index ?>][title]" data-quill="1" class="form-control mb-2" rows="2"><?= htmlspecialchars($item->title ?? '') ?></textarea>
                    <textarea name="items[openingTime][<?= (int)$index ?>][content]" data-quill="1" class="form-control" rows="2"><?= htmlspecialchars($item->content ?? '') ?></textarea>
                </div>
            <?php endforeach; ?>
            <h2 class="h5 mt-4">Opening Times</h2>
            <?php foreach ($openingTimeHours as $index => $item): ?>
                <div class="mb-3">
                    <input type="hidden" name="items[openingTime][<?= (int)$index ?>][id]" value="<?= (int)$item->id ?>">
                    <textarea name="items[openingTime][<?= (int)$index ?>][title]" data-quill="1" class="form-control mb-2" rows="2"><?= htmlspecialchars($item->title ?? '') ?></textarea>
                    <textarea name="items[openingTime][<?= (int)$index ?>][content]" data-quill="1" class="form-control" rows="2"><?= htmlspecialchars($item->content ?? '') ?></textarea>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<?php $tourDetailsJsVersion = @filemtime(__DIR__ . '/../../../../public/js/cms/tour-details.js') ?: time(); ?>
<script src="/js/cms/tour-details.js?v=<?= (int)$tourDetailsJsVersion ?>"></script>