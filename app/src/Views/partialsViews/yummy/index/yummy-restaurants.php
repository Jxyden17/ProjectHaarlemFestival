<section class="restaurants-section">
    <div class="yummy-container">
        <h2 class="section-title"><?= $section->title ?></h2>

        <?php if (!empty($section->items)): ?>
            <?php 
            $venueLookup = [];
            foreach ($venues as $venue) {
                $name = strtolower(trim($venue->venueName ?? ''));
                $venueLookup[$name] = $venue;
            }
            ?>

            <?php foreach ($section->items as $item): ?>
                <?php 
                $itemName = strtolower(trim($item->title ?? ''));
                $venue = $venueLookup[$itemName] ?? null;
                ?>

                <div class="restaurant-card">
                    <div class="restaurant-left">

                        <h3><?= $item->title ?></h3>

                        <?php if (!empty($item->content)): ?>
                            <div class="restaurant-content">
                                <?= $item->content ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($item->image)): ?>
                            <div class="restaurant-stars">
                                <?= $item->image ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($venue && !empty($venue->address)): ?>
                            <p class="restaurant-address">
                                <?= htmlspecialchars($venue->address) ?>
                            </p>
                        <?php endif; ?>

                        <a href="/yummy/<?= strtolower(str_replace(' ', '-', strip_tags($item->title))) ?>" 
                            class="btn-details">
                                View Details →
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No restaurants found.</p>
        <?php endif; ?>
    </div>
</section>