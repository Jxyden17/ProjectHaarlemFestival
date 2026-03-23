<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'CMS') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="/css/Cms/cms-layout.css" rel="stylesheet">
</head>
<body>
<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$isDashboard = $currentPath === '/cms';
$isCms = str_starts_with($currentPath, '/cms');
$isUsers = str_starts_with($currentPath, '/cms/users');
$isEvents = str_starts_with($currentPath, '/cms/events');
$isTickets = str_starts_with($currentPath, '/cms/tickets');
$isEventManagement = str_starts_with($currentPath, '/cms/eventManagement');
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!-- Messages -->
<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
        <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
        <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<div class="container-fluid cms-shell">
    <div class="row">
        <aside class="col-12 col-md-3 col-lg-2 cms-sidebar">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="/cms" class="cms-brand">Festival CMS</a>
            </div>

            <nav class="mt-3">
                <a href="/cms" class="cms-nav-link<?= $isDashboard ? ' is-active' : '' ?>">
                    <i data-lucide="layout-dashboard"></i>
                    Dashboard
                </a>
                <a href="/cms/users" class="cms-nav-link<?= $isUsers ? ' is-active' : '' ?>">
                    <i data-lucide="users"></i>
                    User Management
                </a>
                <a href="/cms/events" class="cms-nav-link<?= $isEvents ? ' is-active' : '' ?>">
                    <i data-lucide="calendar-days"></i>
                    Page Management
                </a>
                <a href="/cms/tickets" class="cms-nav-link<?= $isTickets ? ' is-active' : '' ?>">
                    <i data-lucide="ticket"></i>
                    Ticket Management
                </a>
                <a href="/cms/eventManagement" class="cms-nav-link<?= $isEventManagement ? ' is-active' : '' ?>">
                    <i data-lucide="clapperboard"></i>
                    Event Management
                </a>
            </nav>

            <div class="mt-4 pt-3 border-top border-secondary-subtle">
                <a href="/" class="cms-nav-link">
                    <i data-lucide="house"></i>
                    Back to Site
                </a>
                <a href="/logout" class="cms-nav-link">
                    <i data-lucide="log-out"></i>
                    Logout
                </a>
            </div>
        </aside>

        <main class="col-12 col-md-9 col-lg-10 cms-main px-0">
            <header class="cms-topbar px-4 py-3 d-flex justify-content-between align-items-center">
                <h1 class="cms-topbar-title"><?= htmlspecialchars($title ?? 'CMS') ?></h1>
                <span class="badge text-bg-info">Admin</span>
            </header>

            <section class="cms-content px-3 px-md-4 py-4">
                <?= $content ?>
            </section>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>
</body>
</html>
