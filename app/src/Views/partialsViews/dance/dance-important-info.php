<?php

$importantInfoTitle = trim((string)($importantInfoTitle ?? 'Important Information'));
$importantInfoHtml = (string)($importantInfoHtml ?? '');
$importantInfoItems = array_filter(array_map(
    'trim',
    explode("\n", strip_tags(str_replace(
        ['</li>', '</p>', '</div>', '</ul>', '</ol>', '<br>', '<br/>', '<br />', "\r\n", "\r", "\\r\\n", "\\r", "\\n"],
        ["\n", "\n", "\n", "\n", "\n", "\n", "\n", "\n", "\n", "\n", "\n", "\n", "\n"],
        $importantInfoHtml
    )))
));
?>

<article class="dance-detail-important-card">
    <h3 class="dance-detail-important-title">
        <span class="dance-detail-important-icon"><i data-lucide="star" aria-hidden="true"></i></span>
        <?= htmlspecialchars($importantInfoTitle) ?>
    </h3>

    <?php if (!empty($importantInfoItems)): ?>
        <ul class="dance-detail-important-content">
            <?php foreach ($importantInfoItems as $item): ?>
                <?php if ($item !== ''): ?>
                    <li><?= htmlspecialchars($item) ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <ul class="dance-detail-important-content">
            <li>All shows are 18+ with valid ID required</li>
            <li>Limited capacity - book early to avoid disappointment</li>
            <li>Doors open 1 hour before show time</li>
        </ul>
    <?php endif; ?>
</article>
