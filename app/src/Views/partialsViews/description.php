<section class="discover-section">
    <div class="discover-container">
        <h1 class="discover-title">
            <?= htmlspecialchars($section->title) ?>
        </h1>
        
        <div class="discover-description">
            <?= htmlspecialchars($section->description) ?>
        </div>

        <div class="info-grid">
            <?php foreach ($section->items as $item): ?>
                <div class="info-item">
                    <div class="info-icon"><?= htmlspecialchars($item->image) ?></div>
                    <div class="info-label"><?= htmlspecialchars($item->title) ?></div>
                    <div class="info-value"><?= htmlspecialchars($item->content) ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="pricing-info-row">
                <div class="pricing-section">
                    <h2 class="section-title">Prices</h2>
                    <div class="price-item">
                        <div class="price-label">
                            <span class="price-name">Regular Ticket</span>
                            <span class="price-subtitle">Per person</span>
                        </div>
                        <div class="price-value">€37,50</div>
                    </div>
                    <div class="price-item">
                        <div class="price-label">
                            <span class="price-name">Family Ticket</span>
                            <span class="price-subtitle">2 adults + 2 kids</span>
                        </div>
                        <div class="price-value">€60,00</div>
                    </div>
                </div>
                <div class="info-section">
                    <h2 class="section-title">Important Information</h2>
                    <ul class="info-list">
                        <li>Minimum age: 12 years</li>
                        <li>Strollers are not allowed</li>
                        <li>Group size: 12 participants + 1 guide</li>
                        <li>Breaks at cáfeterías (stop 5)</li>
                    </ul>
                    </div>
                </div>
            </div>
        </section>