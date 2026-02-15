<section class="restaurants-section">
    <div class="yummy-container">
        <h2 class="section-title">Featured Restaurants</h2>

        <?php foreach ($venues as $venue): ?>
            <div class="restaurant-card">
                <div class="restaurant-left">
                    <h3><?= htmlspecialchars($venue->venue_name ?? '') ?></h3>

                    <?php if (!empty($venue->venue_type)): ?>
                        <p class="restaurant-type">
                            <?= htmlspecialchars($venue->venue_type) ?>
                        </p>
                    <?php endif; ?>

                    <div class="restaurant-stars">
                        ★★★★☆
                    </div>

                    <?php if (!empty($venue->address)): ?>
                        <p class="restaurant-address">
                            <?= htmlspecialchars($venue->address) ?>
                        </p>
                    <?php endif; ?>

                    <a href="#" class="btn-details">View Details →</a>
                </div>

                <div class="restaurant-heart">♡</div>
            </div>
        <?php endforeach; ?>
    </div>
</section>