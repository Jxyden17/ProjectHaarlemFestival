<?php

use App\Models\ViewModels\Yummy\YummyIndexViewModel;

if (!$yummyIndexViewModel instanceof YummyIndexViewModel) {
    return;
}

$venues = $yummyIndexViewModel->venues;
$hero = $yummyIndexViewModel->hero;
$map = $yummyIndexViewModel->map;
$restaurants = $yummyIndexViewModel->restaurants;
?>


<link rel="stylesheet" href="/css/Yummy!/index.css">

<?php if ($hero): ?>
    <?php $section = $hero; include __DIR__ . '/../partialsViews/yummy/index/yummy-hero.php'; ?>
<?php endif; ?>

<?php if ($map): ?>
    <?php $section = $map; include __DIR__ . '/../partialsViews/yummy/index/yummy-map.php'; ?>
<?php endif; ?>

<?php if ($restaurants): ?>
    <?php $section = $restaurants; include __DIR__ . '/../partialsViews/yummy/index/yummy-restaurants.php'; ?>
<?php endif; ?>
