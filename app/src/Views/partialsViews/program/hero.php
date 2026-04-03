<?php
$heroSection = $hero ?? null;
$items = $heroSection ? $heroSection->items : [];
?>

<div class="hero">

    <?php foreach ($items as $item): ?>

        <div class="hero-slide">

            <img src="<?= htmlspecialchars($item->image ?? '') ?>" alt="Hero">

            <div class="hero-overlay"></div>

            <div class="hero-content">

                <?php if (!empty($item->title)): ?>
                    <h2 class="hero-title">
                        <?= htmlspecialchars($item->title) ?>
                    </h2>
                <?php endif; ?>

                <?php if (!empty($item->content)): ?>
                    <p class="hero-text">
                        <?= htmlspecialchars($item->content) ?>
                    </p>
                <?php endif; ?>

            </div>
        </div>

    <?php endforeach; ?>

</div>