<?php 
$heroItems = $heroSection?->items ?? [];
$introItems = $introSection?->items ?? [];
$contactItems = $contactSection?->items ?? [];
?>

<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

<div class="container py-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="/cms/events/yummy-home" class="btn btn-outline-secondary"><- Back to Yummy Home</a>
        <h1 class="h3 mb-1">Yummy Subpage: <?= htmlspecialchars($page->slug) ?></h1>
    </div>

    <form method="POST" action="/cms/events/yummy-detail/<?= htmlspecialchars($page->slug) ?>" class="card p-4">

        <section class="mb-5">
            <h2 class="mb-3">Hero Banner</h2>
            <?php if (!empty($heroItems)): ?>
                <?php foreach ($heroItems as $i => $item): ?>
                    <div class="card p-3 mb-3">
                        <label>Banner Image</label>
                        <input type="file" name="items[restaurant_hero][<?= $i ?>][image_path]" class="form-control mb-2">

                        <label>Hero Title</label>
                        <textarea name="sections[restaurant_hero][title]" data-editor="rich" class="form-control mb-2">
                            <?= htmlspecialchars($heroSection?->title ?? '') ?>
                        </textarea>

                        <label>Hero Subtitle</label>
                        <textarea name="sections[restaurant_hero][description]" data-editor="rich" class="form-control mb-2">
                            <?= htmlspecialchars($heroSection?->description ?? '') ?>
                        </textarea>

                        <input type="hidden" name="items[restaurant_hero][<?= $i ?>][id]" value="<?= $item->id ?>">
                        <input type="hidden" name="items[restaurant_hero][<?= $i ?>][order_index]" value="<?= $i ?>">
                        <input type="hidden" name="items[restaurant_hero][<?= $i ?>][item_category]" value="hero">
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <section class="mb-5">
            <h2 class="mb-3">Concept & Festival Menu</h2>
            
            <div class="card p-3 mb-3">
                <label>Concept Title</label>
                <textarea name="sections[restaurant_concept][title]" data-editor="rich" class="form-control mb-2">
                    <?= htmlspecialchars($introSection?->title ?? 'Concept') ?>
                </textarea>

                <label>Concept Description</label>
                <textarea name="sections[restaurant_concept][description]" data-editor="rich" class="form-control mb-2">
                    <?= htmlspecialchars($introSection?->description ?? '') ?>
                </textarea>
            </div>

            <?php if (!empty($introItems)): ?>
                <?php foreach ($introItems as $i => $item): ?>
                    <div class="card p-3 mb-3">
                        <label>Menu Item Name</label>
                        <textarea name="items[restaurant_concept][<?= $i ?>][title]" data-editor="rich" class="form-control mb-2">
                            <?= htmlspecialchars($item->title ?? '') ?>
                        </textarea>

                        <label>Menu Item Description</label>
                        <textarea name="items[restaurant_concept][<?= $i ?>][content]" data-editor="rich" class="form-control mb-2">
                            <?= htmlspecialchars($item->content ?? '') ?>
                        </textarea>

                        <input type="hidden" name="items[restaurant_concept][<?= $i ?>][id]" value="<?= $item->id ?>">
                        <input type="hidden" name="items[restaurant_concept][<?= $i ?>][order_index]" value="<?= $i ?>">
                        <input type="hidden" name="items[restaurant_concept][<?= $i ?>][item_category]" value="concept">
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <section class="mb-5">
            <h2 class="mb-3">Contact & Location</h2>
            
            <div class="card p-3 mb-3">
                <label>Contact Title</label>
                <textarea name="sections[restaurant_contact][title]" data-editor="rich" class="form-control mb-2">
                    <?= htmlspecialchars($contactSection?->title ?? 'Contact & Location') ?>
                </textarea>
            </div>

            <?php if (!empty($contactItems)): ?>
                <?php foreach ($contactItems as $i => $item): ?>
                    <div class="card p-3 mb-3">
                        <label>Location / Phone</label>
                        <textarea name="items[restaurant_contact][<?= $i ?>][title]" data-editor="rich" class="form-control mb-2">
                            <?= htmlspecialchars($item->title ?? '') ?>
                        </textarea>

                        <label>Details</label>
                        <textarea name="items[restaurant_contact][<?= $i ?>][content]" data-editor="rich" class="form-control">
                            <?= htmlspecialchars($item->content ?? '') ?>
                        </textarea>

                        <input type="hidden" name="items[restaurant_contact][<?= $i ?>][id]" value="<?= $item->id ?>">
                        <input type="hidden" name="items[restaurant_contact][<?= $i ?>][order_index]" value="<?= $i ?>">
                        <input type="hidden" name="items[restaurant_contact][<?= $i ?>][item_category]" value="contact">
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <button class="btn btn-primary mt-3">Save Subpage</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script src="/js/cms/yummy-detail.js"></script>