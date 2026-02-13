<section class="contact-details-section">
    <div class="contact-container">
        <div class="map-wrapper">
            <iframe 
                width="100%" 
                height="100%" 
                frameborder="0" 
                scrolling="no" 
                marginheight="0" 
                marginwidth="0" 
                src="<?= $section->items[0]->content ?>" 
                style="border: 0; min-height: 450px; width: 100%;">
            </iframe>
        </div>

        <div class="info-wrapper-box">
            <div class="entrance-image">
                <img src="<?= htmlspecialchars($section->items[1]->image) ?>" alt="Entrance">
            </div>
            
            <div class="details-flex">
                <div class="contact-col">
                    <h3 class="info-title">Contact</h3>
                    <div class="wysiwyg-content"><?= $section->items[1]->content ?></div>
                </div>
                
                <div class="hours-col">
                    <h3 class="info-title">Opening Hours</h3>
                    <div class="wysiwyg-content"><?= $section->items[2]->content ?></div>
                </div>
            </div>
        </div>
    </div>
</section>