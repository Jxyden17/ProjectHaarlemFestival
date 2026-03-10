<?php
use App\Models\ViewModels\Shared\ScheduleViewModel;

$scheduleData = $scheduleData ?? null;
$scheduleSectionClass = is_string($scheduleSectionClass ?? null) ? trim((string)$scheduleSectionClass) : '';
$scheduleTitleIcon = trim((string)($scheduleTitleIcon ?? ''));
$scheduleHasIcons = filter_var($scheduleHasIcons ?? false, FILTER_VALIDATE_BOOLEAN);

if (!$scheduleData instanceof ScheduleViewModel) {
    return;
}

$title = $scheduleData->title;
$dayFilters = $scheduleData->dayFilters;
$groups = $scheduleData->groups;
?>
<section class="schedule-section<?= $scheduleSectionClass === '' ? '' : ' ' . htmlspecialchars($scheduleSectionClass) ?>">
    <div class="schedule-container">
        <div class="schedule-header">
            <h2 class="schedule-title<?= $scheduleTitleIcon === '' ? '' : ' schedule-title--with-icon' ?>">
                <?php if ($scheduleTitleIcon !== ''): ?>
                    <i data-lucide="<?= htmlspecialchars($scheduleTitleIcon) ?>" aria-hidden="true"></i>
                <?php endif; ?>
                <?= htmlspecialchars($title) ?>
            </h2>
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

        <div class="schedule-list">
            <div class="schedule-row schedule-row-head">
                <div>DATE</div>
                <div>TIME</div>
                <div>EVENT</div>
                <div>LOCATION</div>
                <div>PRICE</div>
                <div></div>
            </div>

            <?php foreach ($groups as $group): ?>
                <div class="schedule-day-group" data-day="<?= htmlspecialchars($group->dayKey) ?>">
                    <h3 class="schedule-day-title"><?= htmlspecialchars($group->title) ?></h3>
                    <div class="schedule-day-subtitle"><?= htmlspecialchars($group->subtitle) ?></div>

                    <?php foreach ($group->rows as $row): ?>
                        <div class="schedule-row">
                            <div class="<?= $scheduleHasIcons ? 'schedule-cell schedule-cell--date' : '' ?>">
                                <?php if ($scheduleHasIcons): ?>
                                    <span class="schedule-cell-icon"><i data-lucide="calendar" aria-hidden="true"></i></span>
                                <?php endif; ?>
                                <span><?= htmlspecialchars($row->date) ?></span>
                            </div>
                            <div class="<?= $scheduleHasIcons ? 'schedule-cell schedule-cell--time' : '' ?>">
                                <?php if ($scheduleHasIcons): ?>
                                    <span class="schedule-cell-icon"><i data-lucide="clock-3" aria-hidden="true"></i></span>
                                <?php endif; ?>
                                <span><?= htmlspecialchars($row->time) ?></span>
                            </div>
                            <div><?= htmlspecialchars($row->event) ?></div>
                            <div class="<?= $scheduleHasIcons ? 'schedule-cell schedule-cell--location' : '' ?>">
                                <?php if ($scheduleHasIcons): ?>
                                    <span class="schedule-cell-icon"><i data-lucide="map-pin" aria-hidden="true"></i></span>
                                <?php endif; ?>
                                <span><?= htmlspecialchars($row->location) ?></span>
                            </div>
                            <div class="schedule-price"><?= htmlspecialchars($row->price) ?></div>
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
