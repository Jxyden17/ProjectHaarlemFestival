<section class="restaurants-section">
    <div class="yummy-container">
        <h2 class="section-title"><?= htmlspecialchars($section->title) ?></h2>

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
                        <h3><?= htmlspecialchars($item->title) ?></h3>

                        <?php if (!empty($item->content)): ?>
                            <p class="restaurant-content">
                                <?= htmlspecialchars($item->content) ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($item->image)): ?>
                            <p class="restaurant-stars">
                                <?= htmlspecialchars($item->image) ?>
                            </p>
                        <?php endif; ?>

                        <?php if ($venue && !empty($venue->address)): ?>
                            <p class="restaurant-address">
                                <?= htmlspecialchars($venue->address) ?>
                            </p>
                        <?php endif; ?>

                        <a href="#" class="btn-details">View Details →</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No restaurants found.</p>
        <?php endif; ?>
    </div>
</section>