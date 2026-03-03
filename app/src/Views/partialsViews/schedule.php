<?php
use App\Models\ViewModels\Shared\ScheduleViewModel;

$scheduleData = $scheduleData ?? null;

if (!$scheduleData instanceof ScheduleViewModel) {
    return;
}

$title = $scheduleData->title;
$dayFilters = $scheduleData->dayFilters;
$groups = $scheduleData->groups;
$hasLanguage = !empty($scheduleData->languageFilters);
?>
<link rel="stylesheet" href="/css/partialViews/schedule.css">

<section class="schedule-section">
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

        <?php if (!empty($scheduleData->languageFilters)): ?>
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

        <div class="schedule-list <?= $hasLanguage ? 'with-language' : 'no-language' ?>">
            <div class="schedule-row schedule-row-head">
                <div>DATE</div>
                <div>TIME</div>
                <div>EVENT</div>
                <div>LOCATION</div>
                <div>PRICE</div>
                <?php if ($hasLanguage): ?>
                    <div>LANGUAGE</div>
                    <div>AVAILABILITY</div>
                <?php endif; ?>
                <div></div>
            </div>

            <?php foreach ($groups as $group): ?>
                <div class="schedule-day-group" data-day="<?= htmlspecialchars($group->dayKey) ?>">
                    <h3 class="schedule-day-title"><?= htmlspecialchars($group->title) ?></h3>
                    <div class="schedule-day-subtitle"><?= htmlspecialchars($group->subtitle) ?></div>

                    <?php foreach ($group->rows as $row): ?>
                        <div class="schedule-row" <?= $hasLanguage ? 'data-language="' . htmlspecialchars($langSlug) . '"' : '' ?> >
                            <div><?= htmlspecialchars($row->date) ?></div>
                            <div><?= htmlspecialchars($row->time) ?></div>
                            <div><?= htmlspecialchars($row->event) ?></div>
                            <div><?= htmlspecialchars($row->location) ?></div>
                            <div class="schedule-price"><?= htmlspecialchars($row->price) ?></div>
                            <?php if ($hasLanguage): ?>
                                <div><?= htmlspecialchars($row->language ?? 'Unknown') ?></div>
                                <div><?= htmlspecialchars(($row->totalTickets ?? 0) . ' / ' . ($row->availableTickets ?? 0)) ?></div>
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
