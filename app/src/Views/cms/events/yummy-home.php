<?php
$heroItems = $heroSection?->items ?? [];
$mapItems = $mapSection?->items ?? [];
$restaurantItems = $restaurantSection?->items ?? [];
?>

<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

<div class="container py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="/cms/events" class="btn btn-outline-secondary"><- Back to Events</a>
    </div>

    <form method="POST" action="/cms/events/yummy-home" class="card">
        <section class="mb-5">
            <h2>Hero Banner</h2>

            <textarea
                name="sections[yummy_header][title]"
                data-quill="1"
                class="form-control"
            ><?= htmlspecialchars($heroSection?->title) ?></textarea>

            <textarea
                name="sections[yummy_header][description]"
                data-quill="1"
                class="form-control mt-3"
            ><?= htmlspecialchars($heroSection?->description) ?></textarea>

            <?php foreach ($heroItems as $i => $item): ?>
                <input type="hidden"
                    name="items[yummy_header][<?= $i ?>][id]"
                    value="<?= $item->id ?>"
                >

                <div class="mt-3">
                    <label>Banner image</label>
                    <input type="file" class="form-control">
                </div>

                <input type="hidden"
                    name="items[yummy_header][<?= $i ?>][order_index]"
                    value="<?= $i ?>"
                >

                <input type="hidden"
                    name="items[yummy_header][<?= $i ?>][item_category]"
                    value="hero"
                >
            <?php endforeach; ?>
        </section>

        <section class="mb-5">
            <h2>Map Section</h2>

            <textarea
                name="sections[yummy-map][title]"
                data-quill="1"
                class="form-control"
            ><?= htmlspecialchars($mapSection?->title) ?></textarea>

            <?php foreach ($mapItems as $i => $item): ?>

                <input type="hidden"
                    name="items[yummy-map][<?= $i ?>][id]"
                    value="<?= $item->id ?>"
                >

                <div class="mt-3">

                    <label>Map iframe</label>

                    <textarea
                        name="items[yummy-map][<?= $i ?>][content]"
                        class="form-control"
                        rows="3"
                    ><?= htmlspecialchars($item->content) ?></textarea>

                    <label class="mt-2">Description</label>

                    <textarea
                        name="items[yummy-map][<?= $i ?>][item_subtitle]"
                        data-quill="1"
                        class="form-control"
                    ><?= htmlspecialchars($item->subTitle) ?></textarea>

                    <input type="hidden"
                        name="items[yummy-map][<?= $i ?>][order_index]"
                        value="<?= $i ?>"
                    >

                    <input type="hidden"
                        name="items[yummy-map][<?= $i ?>][item_category]"
                        value="map"
                    >
                </div>
            <?php endforeach; ?>
        </section>

        <section>

            <h2>Restaurants</h2>

            <textarea
                name="sections[yummy-restaurants][title]"
                data-quill="1"
                class="form-control mb-4"
            ><?= htmlspecialchars($restaurantSection?->title) ?></textarea>

            <?php foreach ($restaurantItems as $i => $item): ?>

                <input type="hidden"
                    name="items[yummy-restaurants][<?= $i ?>][id]"
                    value="<?= $item->id ?>"
                >

                <div class="card p-3 mb-3">

                    <label>Name</label>

                    <textarea
                        name="items[yummy-restaurants][<?= $i ?>][title]"
                        data-quill="1"
                        class="form-control"
                    ><?= htmlspecialchars($item->title) ?></textarea>

                    <label class="mt-2">Description</label>

                    <textarea
                        name="items[yummy-restaurants][<?= $i ?>][content]"
                        data-quill="1"
                        class="form-control"
                    ><?= htmlspecialchars($item->content) ?></textarea>

                    <label class="mt-2">Stars / Rating</label>

                    <textarea
                        name="items[yummy-restaurants][<?= $i ?>][image_path]"
                        data-quill="1"
                        class="form-control"
                    ><?= htmlspecialchars($item->image ?? '') ?></textarea>
                    
                    <input type="hidden"
                        name="items[yummy-restaurants][<?= $i ?>][order_index]"
                        value="<?= $i ?>"
                    >

                    <input type="hidden"
                        name="items[yummy-restaurants][<?= $i ?>][item_category]"
                        value="restaurant"
                    >
                </div>

            <?php endforeach; ?>

        </section>

        <button class="btn btn-primary mt-4">
            Save Yummy Page
        </button>

    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script src="/js/cms/page-editor.js"></script>
<script src="/js/cms/yummy-home.js"></script>