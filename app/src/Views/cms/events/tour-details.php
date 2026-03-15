<?php
$heroItems = $header?->items ?? [];
$historyItems = $history?->items ?? [];
$didYouKnowItems = $didYouKnow?->items ?? [];
$openingTimeInfo = $openingTime->getItemsByCategorie('info') ?? [];
$openingTimeHours = $openingTime->getItemsByCategorie('opening_hours') ?? [];
?>

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
                <input id="hero_title" name="sections[header][title]" class="form-control" rows="2" value="<?= htmlspecialchars($header?->title) ?>">
                <label for="hero_subtitle" class="form-label">SubTitle</label>
                <input id="hero_subtitle" name="sections[header][subtitle]" class="form-control" rows="2" value="<?= htmlspecialchars($header?->subTitle) ?>">
                <input type="hidden" name="sections[header][description]" value="<?= htmlspecialchars($header?->description ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Fotos</label>
                <?php foreach ($heroItems as $index => $item): ?>
                    <div class="d-flex flex-wrap gap-2 align-items-center performer-image-row mb-2">
                        <input type="hidden" name="items[header][<?= (int)$index ?>][id]" class="tour-item-id" value="<?= $item->id ?>">
                        <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                        <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                        <a href="<?= $item->image ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                        <input type="hidden" name="items[header][<?= (int)$index ?>][title]" value="<?= htmlspecialchars($item->title ?? '') ?>">
                        <input type="hidden" name="items[header][<?= (int)$index ?>][content]" value="<?= htmlspecialchars($item->content ?? '') ?>">
                        <input type="hidden" name="items[header][<?= (int)$index ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                        <input type="hidden" name="items[header][<?= (int)$index ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                        <input type="hidden" name="items[header][<?= (int)$index ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                        <input type="hidden" name="items[header][<?= (int)$index ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
                    </div>
                <?php endforeach; ?>
            </div>
            
            <h2 class="h5 mt-4">History</h2>
            <label class="form-label">Section Title</label>
            <input type="text" name="sections[history][title]" class="form-control mb-2" value="<?= htmlspecialchars($history?->title) ?>">
            <input type="hidden" name="sections[history][subtitle]" value="<?= htmlspecialchars($history?->subTitle ?? '') ?>">
            <input type="hidden" name="sections[history][description]" value="<?= htmlspecialchars($history?->description ?? '') ?>">
            <?php foreach ($historyItems as $index => $item): ?>
                <div class="mb-3">
                    <input type="hidden" name="items[history][<?= $index ?>][id]" class="tour-item-id" value="<?= (int)$item->id ?>">
                    <label class="form-label">Title</label>
                    <input type="text" name="items[history][<?= $index ?>][title]" class="form-control mb-2" value="<?= htmlspecialchars($item->title) ?>">
                    <label class="form-label">Foto</label>
                    <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                        <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                        <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>                    
                    <label class="form-label">Content</label>
                    <textarea name="items[history][<?= $index ?>][content]" data-quill="1" class="form-control mb-2" rows="2"><?= htmlspecialchars($item->content) ?></textarea>
                    <input type="hidden" name="items[history][<?= (int)$index ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                    <input type="hidden" name="items[history][<?= (int)$index ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                    <input type="hidden" name="items[history][<?= (int)$index ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                    <input type="hidden" name="items[history][<?= (int)$index ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
                </div>
            <?php endforeach; ?>

            <h2 class="h5 mt-4">Did You Know</h2>
            <label class="form-label">Section Title</label>
            <input type="text" name="sections[did_you_know][title]" class="form-control mb-3" value="<?= htmlspecialchars($didYouKnow?->title ?? '') ?>">
            <input type="hidden" name="sections[did_you_know][subtitle]" value="<?= htmlspecialchars($didYouKnow?->subTitle ?? '') ?>">
            <input type="hidden" name="sections[did_you_know][description]" value="<?= htmlspecialchars($didYouKnow?->description ?? '') ?>">
            <?php foreach ($didYouKnowItems as $index => $item): ?>
                <div class="mb-3">
                    <input type="hidden" name="items[did_you_know][<?= (int)$index ?>][id]" class="tour-item-id" value="<?= (int)$item->id ?>">
                    <label class="form-label">Title</label>
                    <input type="text" name="items[did_you_know][<?= (int)$index ?>][title]" class="form-control mb-2" value="<?= htmlspecialchars($item->title) ?>">
                    <label class="form-label">Foto</label>
                    <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                        <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                        <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                    <label class="form-label">Content</label>
                    <textarea name="items[did_you_know][<?= (int)$index ?>][content]" data-quill="1" class="form-control mb-2" rows="2"><?= htmlspecialchars($item->content) ?></textarea>
                    <input type="hidden" name="items[did_you_know][<?= (int)$index ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                    <input type="hidden" name="items[did_you_know][<?= (int)$index ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                    <input type="hidden" name="items[did_you_know][<?= (int)$index ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                    <input type="hidden" name="items[did_you_know][<?= (int)$index ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
                </div>
            <?php endforeach; ?>


            <h2 class="h5 mt-4">MapSections</h2>
            <label class="form-label">Title</label>
            <input type="text" name="sections[openings_time][title]" class="form-control mb-2" value="<?= htmlspecialchars($openingTime?->title) ?>">
            <input type="hidden" name="sections[openings_time][subtitle]" value="<?= htmlspecialchars($openingTime?->subTitle) ?>">
            <input type="text" name="sections[openings_time][description]" class="form-control mb-3" value="<?= htmlspecialchars($openingTime?->description) ?>">
            <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                        <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                        <a href="<?= htmlspecialchars((string)($openingTime?->image ?? '')) ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= (($openingTime?->image ?? '') === '') ? ' d-none' : '' ?>" download>Download</a>
            <h2 class="h5 mt-4">Informations</h2>
            <?php foreach ($openingTimeInfo as $index => $item): ?>
                <?php $rowIndex = $index; ?>
                <div class="mb-3">
                    <input type="hidden" name="items[openings_time][<?= (int)$rowIndex ?>][id]" value="<?= (int)$item->id ?>">
                    <label class="form-label">Title</label>
                    <input type="text" name="items[openings_time][<?= (int)$rowIndex ?>][title]" class="form-control mb-2" value="<?= htmlspecialchars($item->title) ?>">
                    <label class="form-label">Content</label>
                    <input type="text" name="items[openings_time][<?= (int)$rowIndex ?>][content]" class="form-control" value="<?= htmlspecialchars($item->content) ?>">
                    <input type="hidden" name="items[openings_time][<?= (int)$rowIndex ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                    <input type="hidden" name="items[openings_time][<?= (int)$rowIndex ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                    <input type="hidden" name="items[openings_time][<?= (int)$rowIndex ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                    <input type="hidden" name="items[openings_time][<?= (int)$rowIndex ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
                </div>
            <?php endforeach; ?>
            <h2 class="h5 mt-4">Opening Times</h2>
            <?php foreach ($openingTimeHours as $index => $item): ?>
                <?php $rowIndex = $index + 100; ?>
                <div class="mb-3">
                    <input type="hidden" name="items[openings_time][<?= (int)$rowIndex ?>][id]" value="<?= (int)$item->id ?>">
                    <label class="form-label">Dag</label>
                    <input type="text" name="items[openings_time][<?= (int)$rowIndex ?>][title]" class="form-control mb-2" value="<?= htmlspecialchars($item->title) ?>">
                    <label class="form-label">Tijd</label>
                    <input type="text" name="items[openings_time][<?= (int)$rowIndex ?>][content]" class="form-control" value="<?= htmlspecialchars($item->content) ?>">
                    <input type="hidden" name="items[openings_time][<?= (int)$rowIndex ?>][link_url]" value="<?= htmlspecialchars($item->url ?? '') ?>">
                    <input type="hidden" name="items[openings_time][<?= (int)$rowIndex ?>][duration]" value="<?= htmlspecialchars($item->duration ?? '') ?>">
                    <input type="hidden" name="items[openings_time][<?= (int)$rowIndex ?>][icon_class]" value="<?= htmlspecialchars($item->icon ?? '') ?>">
                    <input type="hidden" name="items[openings_time][<?= (int)$rowIndex ?>][item_subtitle]" value="<?= htmlspecialchars($item->subTitle ?? '') ?>">
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary save-btn">Save Changes</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<?php $tourDetailsJsVersion = @filemtime(__DIR__ . '/../../../../public/js/cms/tour-details.js') ?: time(); ?>
<script src="/js/cms/tour-details.js?v=<?= (int)$tourDetailsJsVersion ?>"></script>