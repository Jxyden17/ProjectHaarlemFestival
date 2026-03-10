<section class="restaurant-card">

<h2><?= htmlspecialchars($section->title ?? '') ?></h2>

<?php foreach ($section->items as $item): ?>

<div class="contact-item">

    <?php if (!empty($item->icon ?? '')): ?>
        <span class="restaurant-icon"><?= htmlspecialchars($item->icon) ?></span>
    <?php endif; ?>

    <?php if (!empty($item->title)): ?>
        <p class="contact-title"><?= htmlspecialchars($item->title) ?></p>
    <?php endif; ?>

    <?php if (!empty($item->content ?? '')): ?>
        <p class="contact-text"><?= htmlspecialchars($item->content) ?></p>
    <?php endif; ?>

    <?php if (!empty($item->url)): ?>
        <div class="map-wrapper">
            <iframe
                src="<?= htmlspecialchars($item->url) ?>"
                width="100%"
                height="250"
                loading="lazy">
            </iframe>
        </div>
    <?php endif; ?>

</div>

<?php endforeach; ?>

</section>