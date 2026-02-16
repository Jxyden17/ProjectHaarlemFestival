(() => {
    const scheduleSections = document.querySelectorAll('.schedule-section');

    if (!scheduleSections.length) {
        return;
    }

    scheduleSections.forEach((section) => {
        const filterButtons = section.querySelectorAll('.schedule-filter-btn');
        const dayGroups = section.querySelectorAll('.schedule-day-group');

        if (!filterButtons.length || !dayGroups.length) {
            return;
        }

        const applyFilter = (dayKey) => {
            dayGroups.forEach((group) => {
                const groupDay = group.getAttribute('data-day');
                const shouldShow = dayKey === 'all' || dayKey === groupDay;
                group.style.display = shouldShow ? '' : 'none';
            });
        };

        filterButtons.forEach((button) => {
            button.addEventListener('click', () => {
                filterButtons.forEach((btn) => btn.classList.remove('is-active'));
                button.classList.add('is-active');

                const dayKey = button.getAttribute('data-filter') || 'all';
                applyFilter(dayKey);
            });
        });
    });
})();
