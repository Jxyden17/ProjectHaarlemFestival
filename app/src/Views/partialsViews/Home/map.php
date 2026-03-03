<section class="home-map-section">
    <div class="container map-box">
        <div class="map-wrapper">
            <iframe id="festival-map-iframe" name="festival-map-frame" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2435.3066019031385!2d4.6286359!3d52.382991999999994!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5efb0eb820119%3A0x3469bc95e9c3bc06!2sPatronaat!5e0!3m2!1spl!2snl!4v1772521754443!5m2!1spl!2snl" frameborder="0" style="border:0; width:100%; height:100%;" allowfullscreen="" loading="lazy"></iframe>
        </div>

        <div class="map-filters">
            <label class="map-filters-label"><?= htmlspecialchars($section->title ?? 'Filter by Category') ?></label>
            <div class="map-filters-list">
                <?php foreach ($section->getItemsByCategorie('map_location') as $item): ?>
                    <?php $href = $item->content; ?>
                    <a class="map-filter-link" href="<?= htmlspecialchars($href) ?>" target="festival-map-frame"><?= htmlspecialchars($item->title) ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<script>
    // Script to toggle active class on clicked filter
    (function(){
        const links = Array.from(document.querySelectorAll('.map-filter-link'));
        if (!links.length) return;

        function setActive(clicked) {
            links.forEach(l => l.classList.toggle('active', l === clicked));
        }

        setActive(links[0]);

        links.forEach(link => {
            link.addEventListener('click', function(){
                setActive(link);
            });
        });
    })();
</script>
