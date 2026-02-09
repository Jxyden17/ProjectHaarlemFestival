<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'Project Template' ?></title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600&family=Playfair+Display:wght@400;600;700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
    <link href="/css/styles.css" rel="stylesheet">
</head>
<body class="bg-light hf-page">

<?php $isLoggedIn = isset($_SESSION['user_id']); ?>

<nav class="hf-navbar">
    <div class="container hf-nav-inner">
        <a class="hf-brand" href="/" aria-label="Visit Haarlem Festival">
            <img src="/img/logo.png" alt="Visit Haarlem Festival" class="hf-logo">
        </a>

        <ul class="hf-nav-links">
            <li>
                <a href="/">
                    <span class="hf-icon" aria-hidden="true">
                        <i data-lucide="home"></i>
                    </span>
                    Home
                </a>
            </li>
            <li>
                <a href="/dance">
                    <span class="hf-icon" aria-hidden="true">
                        <i data-lucide="music"></i>
                    </span>
                    Dance
                </a>
            </li>
            <li>
                <a href="/tour">
                    <span class="hf-icon" aria-hidden="true">
                        <i data-lucide="map"></i>
                    </span>
                    Tour
                </a>
            </li>
            <li>
                <a href="/stories">
                    <span class="hf-icon" aria-hidden="true">
                        <i data-lucide="book-open"></i>
                    </span>
                    Stories
                </a>
            </li>
            <li>
                <a href="/jazz">
                    <span class="hf-icon" aria-hidden="true">
                        <i data-lucide="music-2"></i>
                    </span>
                    Jazz
                </a>
            </li>
            <li>
                <a href="/yummy">
                    <span class="hf-icon" aria-hidden="true">
                        <i data-lucide="utensils"></i>
                    </span>
                    Yummy
                </a>
            </li>
            <li>
                <a href="/contact">
                    <span class="hf-icon" aria-hidden="true">
                        <i data-lucide="mail"></i>
                    </span>
                    Contact
                </a>
            </li>
        </ul>

        <div class="hf-nav-actions">
            <a class="hf-btn" href="/book">Book Now</a>
            <a class="hf-icon-btn" href="/favorites" aria-label="Favorites">
                <i data-lucide="heart"></i>
            </a>
            <a class="hf-icon-btn" href="/cart" aria-label="Cart">
                <i data-lucide="shopping-cart"></i>
            </a>
            <?php if ($isLoggedIn): ?>
                <a class="hf-icon-btn" href="/logout" aria-label="Logout">
                    <i data-lucide="log-out"></i>
                </a>
            <?php else: ?>
                <a class="hf-icon-btn" href="/login" aria-label="Login">
                    <i data-lucide="log-in"></i>
                </a>
            <?php endif; ?>
            <span class="hf-lang">ENG</span>
        </div>
    </div>
</nav>

<div class="container hf-page-content">
    <?= $content ?>
</div>

<?php require __DIR__ . '/partialsViews/footer.php'; ?>

<!-- Bootstrap JS Bundle CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>
</body>
</html>
