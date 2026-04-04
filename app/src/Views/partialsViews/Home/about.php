<?php
use App\Models\Page\Section;
$section = $section ?? null;

if (!$section instanceof Section) {
    return;
}
?>
<section class="about-section">
    <div class="container">
        <div class="about-wrapper">
            <div class="about-content">
                <h2 class="about-title"><?= htmlspecialchars($section->title ?? '') ?></h2>
                
                <div class="about-text">
                    <p class="main-about-text"><?= htmlspecialchars($section->subTitle ?? '') ?></p>
                    
                    <p class="location-text"><?= htmlspecialchars($section->description ?? '') ?></p>
                </div>
                <div class="about-features">
                    <?php 
                    $labels = $section->getItemsByCategorie('label'); 
                    if (!empty($labels)): 
                        foreach($labels as $label): 
                    ?>
                        <span class="feature-tag">
                            <span class="tag-icon"><?= htmlspecialchars($label->icon ?? $label->subTitle ?? '') ?></span>
                            <?= htmlspecialchars($label->title ?? '') ?>
                        </span>
                    <?php 
                        endforeach; 
                    endif; 
                    ?>
                </div>
            </div>
            </div>
        </div>
</section>