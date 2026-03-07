(() => {
    const scheduleSections = document.querySelectorAll('.schedule-section');

    if (!scheduleSections.length) {
        return;
    }

    scheduleSections.forEach((section) => {
        const dayFilterButtons = section.querySelectorAll('.schedule-filter-btn[data-filter]');
        const eventFilterButtons = section.querySelectorAll('.schedule-event-filter-btn[data-event]');
        const languageFilterButtons = section.querySelectorAll('.schedule-language-filter-btn[data-language]');
        const dayGroups = section.querySelectorAll('.schedule-day-group');

        if (!dayFilterButtons.length || !dayGroups.length) {
            return;
        }

        let activeDay = section.querySelector('.schedule-filter-btn[data-filter].is-active')?.getAttribute('data-filter') || 'all';
        let activeEvent = section.querySelector('.schedule-event-filter-btn[data-event].is-active')?.getAttribute('data-event') || 'all';
        let activeLanguage = section.querySelector('.schedule-language-filter-btn[data-language].is-active')?.getAttribute('data-language') || 'all';

        const applyFilters = () => {
            dayGroups.forEach((group) => {
                const groupDay = group.getAttribute('data-day');
                const matchesDay = activeDay === 'all' || activeDay === groupDay;
                const rows = group.querySelectorAll('.schedule-row');
                let hasVisibleRows = false;

                rows.forEach((row) => {
                    const rowLanguage = (row.getAttribute('data-language') || 'unknown').toLowerCase();
                    const rowEvent = (row.getAttribute('data-event') || 'other').toLowerCase();

                    const matchesEvent = !eventFilterButtons.length || activeEvent === 'all' || activeEvent === rowEvent;
                    const matchesLanguage = activeLanguage === 'all' || activeLanguage === rowLanguage;
                    const shouldShowLanguage = !languageFilterButtons.length || matchesLanguage;
                    const shouldShowRow = matchesDay && matchesEvent && shouldShowLanguage;
                    row.style.display = shouldShowRow ? '' : 'none';

                    if (shouldShowRow) {
                        hasVisibleRows = true;
                    }
                });

                group.style.display = hasVisibleRows ? '' : 'none';
            });
        };

        dayFilterButtons.forEach((button) => {
            button.addEventListener('click', () => {
                dayFilterButtons.forEach((btn) => btn.classList.remove('is-active'));
                button.classList.add('is-active');
                activeDay = button.getAttribute('data-filter') || 'all';
                applyFilters();
            });
        });

        eventFilterButtons.forEach((button) => {
            button.addEventListener('click', () => {
                eventFilterButtons.forEach((btn) => btn.classList.remove('is-active'));
                button.classList.add('is-active');
                activeEvent = button.getAttribute('data-event') || 'all';
                applyFilters();
            });
        });

        languageFilterButtons.forEach((button) => {
            button.addEventListener('click', () => {
                languageFilterButtons.forEach((btn) => btn.classList.remove('is-active'));
                button.classList.add('is-active');
                activeLanguage = button.getAttribute('data-language') || 'all';
                applyFilters();
            });
        });

        applyFilters();
    });
})();
