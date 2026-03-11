<?php

use App\Models\Page\Page;

if (!$viewModel instanceof Page) {
    return;
}

$sections = $viewModel->sections ?? [];

?>

<link rel="stylesheet" href="/css/Yummy!/restaurant.css">

<a href="/yummy" class="back-link">← Back to Overview</a>

<?php foreach ($sections as $section): ?>

    <?php
        $sectionType = strtolower($section->type);
        $partial = __DIR__ . "/../partialsViews/yummy/restaurant/{$sectionType}.php";
    ?>

    <?php if (file_exists($partial)): ?>
        <?php include $partial; ?>
    <?php endif; ?>

<?php endforeach; ?>