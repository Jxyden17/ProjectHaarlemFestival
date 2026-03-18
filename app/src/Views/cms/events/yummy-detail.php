<?php
$heroItems = $heroSection?->items ?? [];
$introItems = $introSection?->items ?? [];
$contactItems = $contactSection?->items ?? [];
?>

<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

<div class="container py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="/cms/events" class="btn btn-outline-secondary"><- Back to Events</a>
        <h1 class="h3 mb-1">Yummy Subpage: <?= htmlspecialchars($page->slug) ?></h1>
    </div>

    <form method="POST" action="/cms/events/yummy-detail/<?= htmlspecialchars($page->slug) ?>" class="card">
        
        <section class="mb-5">
            <h2>Hero Banner</h2>

            <textarea
                name="sections[restaurant_hero][title]"
                data-editor="rich"
                class="form-control"
            ><?= htmlspecialchars($heroSection?->title) ?></textarea>

            <?php foreach ($heroItems as $i => $item): ?>
                <input type="hidden"
                    name="items[restaurant_hero][<?= $i ?>][id]"
                    value="<?= $item->id ?>"
                >

                <div class="mt-3">
                    <label>Banner image</label>
                    <input type="file" class="form-control">
                </div>

                <input type="hidden"
                    name="items[restaurant_hero][<?= $i ?>][order_index]"
                    value="<?= $i ?>"
                >

                <input type="hidden"
                    name="items[restaurant_hero][<?= $i ?>][item_category]"
                    value="hero"
                >
            <?php endforeach; ?>
        </section>

        <section class="mb-5">
            <h2>Concept</h2>

            <textarea
                name="sections[restaurant_concept][title]"
                data-editor="rich"
                class="form-control"
            ><?= htmlspecialchars($introSection?->title) ?></textarea>

            <textarea
                name="sections[restaurant_concept][description]"
                data-editor="rich"
                class="form-control mt-3"
            ><?= htmlspecialchars($introSection?->description) ?></textarea>

            <?php foreach ($introItems as $i => $item): ?>

                <input type="hidden"
                    name="items[restaurant_concept][<?= $i ?>][id]"
                    value="<?= $item->id ?>"
                >

                <div class="mt-3">

                    <label>Menu Item Name</label>

                    <textarea
                        name="items[restaurant_concept][<?= $i ?>][title]"
                        data-editor="rich"
                        class="form-control"
                    ><?= htmlspecialchars($item->title) ?></textarea>

                    <label class="mt-2">Menu Item Description</label>

                    <textarea
                        name="items[restaurant_concept][<?= $i ?>][content]"
                        data-editor="rich"
                        class="form-control"
                    ><?= htmlspecialchars($item->content) ?></textarea>

                    <input type="hidden"
                        name="items[restaurant_concept][<?= $i ?>][order_index]"
                        value="<?= $i ?>"
                    >

                    <input type="hidden"
                        name="items[restaurant_concept][<?= $i ?>][item_category]"
                        value="concept"
                    >
                </div>
            <?php endforeach; ?>
        </section>

        <section>

            <h2>Contact Info</h2>

            <textarea
                name="sections[restaurant_contact][title]"
                data-editor="rich"
                class="form-control mb-4"
            ><?= htmlspecialchars($contactSection?->title) ?></textarea>

            <?php foreach ($contactItems as $i => $item): ?>
                <?php if (!empty($item->title) || !empty($item->content)): ?>

                    <input type="hidden"
                        name="items[restaurant_contact][<?= $i ?>][id]"
                        value="<?= $item->id ?>"
                    >

                    <div class="card p-3 mb-3">

                        <label>Location / Phone</label>

                        <textarea
                            name="items[restaurant_contact][<?= $i ?>][title]"
                            data-editor="rich"
                            class="form-control"
                        ><?= htmlspecialchars($item->title) ?></textarea>

                        <label class="mt-2">Details</label>

                        <textarea
                            name="items[restaurant_contact][<?= $i ?>][content]"
                            data-editor="rich"
                            class="form-control"
                        ><?= htmlspecialchars($item->content ?? '') ?></textarea>
                        
                        <input type="hidden"
                            name="items[restaurant_contact][<?= $i ?>][order_index]"
                            value="<?= $i ?>"
                        >

                        <input type="hidden"
                            name="items[restaurant_contact][<?= $i ?>][item_category]"
                            value="contact"
                        >
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

        </section>

        <button class="btn btn-primary mt-4">
            Save Subpage
        </button>

    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<?php $yummyDetailJsVersion = @filemtime(__DIR__ . '/../../../../public/js/cms/yummy-detail.js') ?: time(); ?>
<script src="/js/cms/yummy-detail.js?v=<?= (int)$yummyDetailJsVersion ?>"></script>