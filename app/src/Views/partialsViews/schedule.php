<?php
use App\Models\ViewModels\Shared\ScheduleViewModel;

$scheduleData = $scheduleData ?? null;

if (!$scheduleData instanceof ScheduleViewModel) {
    return;
}

$title = $scheduleData->title;
$eventName = $scheduleData->eventName ?? '';
$normalizedEventName = strtolower(trim($eventName));

if ($normalizedEventName === 'a stroll through history') {
    $eventType = 'tour';
} elseif ($normalizedEventName === 'tellingstory') {
    $eventType = 'stories';
} elseif ($normalizedEventName === 'dance') {
    $eventType = 'dance';
} else {
    $eventType = 'home';
}

$isTour = $eventType === 'tour';
$isStories = $eventType === 'stories';
$isDance = $eventType === 'dance';
$isHome = $eventType === 'home';
$showLanguageFilter = $isTour || $isStories;

$dayFilters = $scheduleData->dayFilters;
$eventFilters = $scheduleData->eventFilters ?? [];
$hasEventFilters = !empty($eventFilters);
$groups = $scheduleData->groups;

$layoutClass = ($isTour || $isStories)
    ? 'tour'
    : ($isHome ? 'home' : 'default');
?>

<link rel="stylesheet" href="/css/partialViews/schedule.css">

<section class="schedule-section schedule-variant-<?= htmlspecialchars($layoutClass) ?>">

    <div class="schedule-container">
        <div class="schedule-header">
            <h2 class="schedule-title"><?= htmlspecialchars($title) ?></h2>
        </div>

        <?php if ($scheduleData->hasFilters): ?>
            <div class="schedule-filters">
                <div class="schedule-filter-group">
                    <?php foreach ($dayFilters as $filter): ?>
                        <button
                            class="schedule-filter-btn<?= $filter->isActive ? ' is-active' : '' ?>"
                            type="button"
                            data-filter="<?= htmlspecialchars($filter->key) ?>"
                        >
                            <?= htmlspecialchars($filter->label) ?>
                            <?= htmlspecialchars($filter->countLabel) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($isHome && $hasEventFilters): ?>
            <div class="schedule-event-filters">
                <div class="schedule-filter-group">
                    <?php foreach ($eventFilters as $filter): ?>
                        <button
                            class="schedule-filter-btn schedule-event-filter-btn<?= $filter->isActive ? ' is-active' : '' ?>"
                            type="button"
                            data-event="<?= htmlspecialchars($filter->key) ?>"
                        >
                            <?= htmlspecialchars($filter->label) ?>
                            <?= htmlspecialchars($filter->countLabel) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($isTour): ?>
            <div class="schedule-language-filters">
                <div class="schedule-filter-group">
                    <?php foreach ($scheduleData->languageFilters as $lfilter): ?>
                        <button
                            class="schedule-filter-btn schedule-language-filter-btn<?= $lfilter->isActive ? ' is-active' : '' ?>"
                            type="button"
                            data-language="<?= htmlspecialchars($lfilter->key) ?>"
                        >
                            <?= htmlspecialchars($lfilter->label) ?>
                            <?= htmlspecialchars($lfilter->countLabel) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="schedule-list <?= $showLanguageFilter ? 'with-language' : 'no-language' ?> schedule-layout-<?= htmlspecialchars($layoutClass) ?>">
            <div class="schedule-row schedule-row-head">
                <?php if ($isHome): ?>
                    <div>EVENT</div>
                    <div>TIME</div>
                    <div>LOCATION</div>
                    <div>NAAM</div>
                    <div>AGE</div>
                    <div>PRICE</div>
                <?php elseif ($isTour || $isStories): ?>
                    <div>DATE</div>
                    <div>TIME</div>
                    <div>LOCATION</div>
                    <?php if ($isTour): ?>
                        <div>LANGUAGE</div>
                        <div>FREE SPOTS</div>
                    <?php endif; ?>
                    <div>PRICE</div>
                <?php elseif ($isDance): ?>
                    <div>DATE</div>
                    <div>TIME</div>
                    <div>EVENT</div>
                    <div>LOCATION</div>
                    <div>PRICE</div>
                <?php endif; ?>
                <div></div>
            </div>

            <?php foreach ($groups as $group): ?>
                <div class="schedule-day-group" data-day="<?= htmlspecialchars($group->dayKey) ?>">
                    <h3 class="schedule-day-title"><?= htmlspecialchars($group->title) ?></h3>
                    <div class="schedule-day-subtitle"><?= htmlspecialchars($group->subtitle) ?></div>

                    <?php foreach ($group->rows as $row): ?>
                        <?php
                            $rowEventName = (string)($row->eventName ?? 'Other');
                            $languageLabel = (string)($row->language ?? 'Unknown');

                            $rowEventKey = strtolower($rowEventName);
                            if ($rowEventKey === '') {
                                $rowEventKey = 'other';
                            }

                            $languageSlug = strtolower($languageLabel);
                            if ($languageSlug === '') {
                                $languageSlug = 'unknown';
                            }

                            $rowAttributes = [];
                            if ($showLanguageFilter) {
                                $rowAttributes[] = 'data-language="' . htmlspecialchars($languageSlug) . '"';
                            }
                            if ($isHome && $hasEventFilters) {
                                $rowAttributes[] = 'data-event="' . htmlspecialchars($rowEventKey) . '"';
                            }
                        ?>
                        <div class="schedule-row" <?= implode(' ', $rowAttributes) ?> >
                            <?php if ($isHome): ?>
                                <div>
                                    <span class="schedule-event-badge schedule-event-<?= htmlspecialchars($rowEventKey) ?>">
                                        <?= htmlspecialchars($rowEventName) ?>
                                    </span>
                                </div>
                                <div><?= htmlspecialchars($row->time) ?></div>
                                <div><?= htmlspecialchars($row->location) ?></div>
                                <div><?= htmlspecialchars($row->event) ?></div>
                                <div>
                                    <span class="schedule-age-badge"><?= htmlspecialchars($row->ageLabel ?? 'N/A') ?></span>
                                </div>
                                <div class="schedule-price"><?= htmlspecialchars($row->price) ?></div>
                            <?php elseif ($isTour || $isStories): ?>
                                <div><?= htmlspecialchars($row->date) ?></div>
                                <div><?= htmlspecialchars($row->time) ?></div>
                                <div><?= htmlspecialchars($row->location) ?></div>
                                <?php if ($showLanguageFilter): ?>

                                    <div>
                                        <span class="schedule-language-badge schedule-language-<?= htmlspecialchars($languageSlug) ?>">
                                            <?= htmlspecialchars($languageLabel) ?>
                                        </span>
                                    </div>
                                    <div><?= htmlspecialchars(($row->availableTickets) . '/' . ($row->totalTickets)) ?></div>
                                <?php endif; ?>
                                <div class="schedule-price"><?= htmlspecialchars($row->price) ?></div>
                            <?php elseif ($isDance): ?>
                                <div><?= htmlspecialchars($row->date) ?></div>
                                <div><?= htmlspecialchars($row->time) ?></div>
                                <div><?= htmlspecialchars($row->event) ?></div>
                                <div><?= htmlspecialchars($row->location) ?></div>
                                <div class="schedule-price"><?= htmlspecialchars($row->price) ?></div>
                            <?php endif; ?>
                            <div>
                                <a class="schedule-book-btn" href="<?= htmlspecialchars($row->bookUrl) ?>">
                                    Book Now
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script src="/js/schedule-filters.js"></script>
