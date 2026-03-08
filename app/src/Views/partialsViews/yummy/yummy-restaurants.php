<section class="restaurants-section">
    <div class="yummy-container">
        <h2 class="section-title"><?= htmlspecialchars($section->title ?? '') ?></h2>

        <?php foreach ($venues as $venue): ?>
            <div class="restaurant-card">
                <div class="restaurant-left">
                    <h3><?= htmlspecialchars($venue->venueName ?? '') ?></h3>

                    <?php if (!empty($venue->venueType)): ?>
                        <p class="restaurant-type">
                            <?= htmlspecialchars($venue->venueType) ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($venue->address)): ?>
                        <p class="restaurant-address">
                            <?= htmlspecialchars($venue->address) ?>
                        </p>
                    <?php endif; ?>

                    <a href="#" class="btn-details">View Details →</a>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
</section>