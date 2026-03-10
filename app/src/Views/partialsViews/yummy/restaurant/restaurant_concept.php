<section class="restaurant-card">

    <h2><?= htmlspecialchars($section->title) ?></h2>

    <p><?= htmlspecialchars($section->description) ?></p>

    <ul class="specials-list">

        <h3><?= htmlspecialchars($section->subTitle) ?></h3>

        <?php foreach ($section->items as $item): ?>
            <li><?= htmlspecialchars($item->content) ?></li>
        <?php endforeach; ?>

    </ul>

</section>