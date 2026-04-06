<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Tour Detail Content of <?= htmlspecialchars($header?->title) ?></h1>
        <a href="/cms/events" class="btn btn-outline-secondary">Back to Events</a>
    </div>

    <?php
    $successMessage = 'Tour detail content updated.';
    include __DIR__ . '/../../partialsViews/cms/form-feedback.php';
    ?>

    <form method="POST" action="/cms/events/tour-details" class="card" data-tour-page-slug="<?= htmlspecialchars($pageSlug ?? 'tour-details') ?>">
        <div class="card-body">
        <input type="hidden" name="page_id" value="<?= $pageId ?? 0 ?>">
            <div class="accordion cms-accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading1">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                        Banner
                        </button>
                    </h2>
                <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="heading1" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <label for="hero_title" class="form-label">Title</label>
                            <input id="hero_title" name="sections[header][title]" class="form-control" rows="2" value="<?= htmlspecialchars($header?->title) ?>">
                            <label for="hero_subtitle" class="form-label">SubTitle</label>
                            <input id="hero_subtitle" name="sections[header][subtitle]" class="form-control" rows="2" value="<?= htmlspecialchars($header?->subTitle) ?>">
                            <input type="hidden" name="sections[header][description]" value="<?= htmlspecialchars($header?->description ?? '') ?>">
                        </div>
 
                        <div class="mb-3">
                            <label class="form-label">Fotos</label>
                            <?php foreach ($header->items ?? [] as $index => $item): ?>
                                <div class="d-flex flex-wrap gap-2 align-items-center performer-image-row mb-2" data-tour-upload-row="1" data-tour-section-type="header" data-tour-item-category="<?= htmlspecialchars((string)($item->category ?? '')) ?>">
                                    <input type="hidden" name="items[header][<?= $index ?>][id]" class="tour-item-id" value="<?= $item->id ?>">
                                    <input type="hidden" name="items[header][<?= $index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                                    <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                                    <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                                    <a href="<?= $item->image ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                                    <input type="hidden" name="items[header][<?= $index ?>][image_path]" class="performer-artist-image" value="<?= htmlspecialchars($item->image ?? '') ?>">
                                    <input type="hidden" name="items[header][<?= $index ?>][title]" value="<?= htmlspecialchars($item->title ?? '') ?>">
                                    <input type="hidden" name="items[header][<?= $index ?>][content]" value="<?= htmlspecialchars($item->content ?? '') ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                        History
                    </button>
                </h2>
                <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                <label class="form-label">Section Title</label>
                        <input type="text" name="sections[history][title]" class="form-control mb-2" value="<?= htmlspecialchars($history?->title) ?>">
                        <?php foreach ($history->items ?? [] as $index => $item): ?>
                            <div class="mb-3" data-tour-upload-row="1" data-tour-section-type="history" data-tour-item-category="<?= htmlspecialchars((string)($item->category ?? '')) ?>">
                                <input type="hidden" name="items[history][<?= $index ?>][id]" class="tour-item-id" value="<?= (int)$item->id ?>">
                                <input type="hidden" name="items[history][<?= $index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                                <input type="hidden" name="items[history][<?= $index ?>][image_path]" class="performer-artist-image" value="<?= htmlspecialchars($item->image ?? '') ?>">
                                <label class="form-label">Title</label>
                                <input type="text" name="items[history][<?= (int)$index ?>][title]" class="form-control mb-2" value="<?= htmlspecialchars($item->title) ?>">
                                <label class="form-label">Foto</label>
                                <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                                    <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                                    <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>                    
                                <label class="form-label">Content</label>
                                <textarea name="items[history][<?= $index ?>][content]" data-quill="1" class="form-control mb-2" rows="2"><?= htmlspecialchars($item->content) ?></textarea>
                            </div>
                        <?php endforeach; ?>
                </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading3">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                    Did You Know
                </button>
                </h2>
                <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="heading3" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <label class="form-label">Section Title</label>
                        <input type="text" name="sections[did_you_know][title]" class="form-control mb-3" value="<?= htmlspecialchars($didYouKnow?->title ?? '') ?>">
                        <?php foreach ($didYouKnow?->items ?? [] as $index => $item): ?>
                            <div class="mb-3" data-tour-upload-row="1" data-tour-section-type="did_you_know" data-tour-item-category="<?= htmlspecialchars((string)($item->category ?? '')) ?>">
                                <input type="hidden" name="items[did_you_know][<?= (int)$index ?>][id]" class="tour-item-id" value="<?= (int)$item->id ?>">
                                <input type="hidden" name="items[did_you_know][<?= (int)$index ?>][image_path]" class="performer-artist-image" value="<?= htmlspecialchars($item->image ?? '') ?>">
                                <input type="hidden" name="items[did_you_know][<?= (int)$index ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                                <label class="form-label">Title</label>
                                <input type="text" name="items[did_you_know][<?= (int)$index ?>][title]" class="form-control mb-2" value="<?= htmlspecialchars($item->title) ?>">
                                <label class="form-label">Foto</label>
                                <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                                    <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                                    <a href="<?= htmlspecialchars($item->image ?? '') ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= ($item->image ?? '') === '' ? ' d-none' : '' ?>" download>Download</a>
                                <label class="form-label">Content</label>
                                <textarea name="items[did_you_know][<?= (int)$index ?>][content]" data-quill="1" class="form-control mb-2" rows="2"><?= htmlspecialchars($item->content) ?></textarea>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading4">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                    MapSections
                </button>
                </h2>
                <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <label class="form-label">Title</label>
                        <input type="text" name="sections[openings_time][title]" class="form-control mb-2" value="<?= htmlspecialchars($openingTime?->title) ?>">
                        <label class="form-label">Embeded map Link</label>
                        <input type="text" name="sections[openings_time][description]" class="form-control mb-3" value="<?= htmlspecialchars($openingTime?->description) ?>">
                        <label class="form-label">Foto of Entery</label>
                        <input type="file" class="form-control form-control-sm performer-upload-input" accept="image/jpeg,image/png,image/webp">
                                    <button type="button" class="btn btn-sm btn-outline-primary upload-performer-image">Upload</button>
                                    <a href="<?= htmlspecialchars((string)($openingTime?->image ?? '')) ?>" class="btn btn-sm btn-outline-secondary performer-download-link<?= (($openingTime?->image ?? '') === '') ? ' d-none' : '' ?>" download>Download</a>
                        <h2 class="h5 mt-4">Informations</h2>
                        <?php foreach ($openingTime->getItemsByCategorie('info') ?? [] as $index => $item): ?>
                            <?php $rowIndex = $index; ?>
                            <div class="d-flex gap-2 mb-2">
                                <input type="hidden" name="items[openings_time][<?= (int)$rowIndex ?>][id]" value="<?= (int)$item->id ?>">
                                <input type="hidden" name="items[openings_time][<?= (int)$rowIndex ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                                <label class="form-label">Title</label>
                                <input type="text" name="items[openings_time][<?= (int)$rowIndex ?>][title]" class="form-control mb-2" value="<?= htmlspecialchars($item->title) ?>">
                                <label class="form-label">Content</label>
                                <input type="text" name="items[openings_time][<?= (int)$rowIndex ?>][content]" class="form-control" value="<?= htmlspecialchars($item->content) ?>">
                            </div>
                        <?php endforeach; ?>
                        <h2 class="h5 mt-4">Opening Times</h2>
                        <?php foreach ($openingTime->getItemsByCategorie('opening_hours') ?? [] as $index => $item): ?>
                            <?php $rowIndex = $index + 100; ?>
                            <div class="d-flex gap-2 mb-2">
                                <input type="hidden" name="items[openings_time][<?= (int)$rowIndex ?>][id]" value="<?= (int)$item->id ?>">
                                <input type="hidden" name="items[openings_time][<?= (int)$rowIndex ?>][item_category]" value="<?= htmlspecialchars($item->category ?? '') ?>">
                                <label class="form-label">Dag:</label>
                                <input type="text" name="items[openings_time][<?= (int)$rowIndex ?>][title]" class="form-control mb-2" value="<?= htmlspecialchars($item->title) ?>">
                                <label class="form-label">Tijd:</label>
                                <input type="text" name="items[openings_time][<?= (int)$rowIndex ?>][content]" class="form-control" value="<?= htmlspecialchars($item->content) ?>">
                            </div>
                        <?php endforeach; ?>
                </div>
                </div>
            </div>
            </div>
            <button type="submit" class="btn btn-primary save-btn">Save Changes</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../partialsViews/cms/upload-feedback-modal.php'; ?>

<script src="/js/cms/upload-feedback.js"></script>
<script src="/js/cms/media-upload.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script src="/js/cms/page-editor.js"></script>
<script src="/js/cms/tour-details.js"></script>
