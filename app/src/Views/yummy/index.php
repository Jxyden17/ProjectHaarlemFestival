<?php

use App\Models\ViewModels\Yummy\YummyIndexViewModel;

$yummyIndexViewModel = $yummyIndexViewModel ?? null;

if (!$yummyIndexViewModel instanceof YummyIndexViewModel) {
    return;
}

$venues = $yummyIndexViewModel->venues;
?>

<link href="/css/Yummy/index.css" rel="stylesheet">

<?php require __DIR__ . '/../partialsViews/yummy/yummy-hero.php'; ?>

<?php require __DIR__ . '/../partialsViews/yummy/yummy-map.php'; ?>

<?php require __DIR__ . '/../partialsViews/yummy/yummy-restaurants.php'; ?>
