<section class="restaurant-card">

    <h2><?= $section->title ?></h2>

    <p><?= $section->description ?></p>

    <ul class="specials-list">

        <h3><?= $section->subTitle ?></h3>

        <?php foreach ($section->items as $item): ?>
            <li><?= $item->content ?></li>
        <?php endforeach; ?>

    </ul>

</section>