document.addEventListener('DOMContentLoaded', () => {

    let selectedDay = 'all';
    let selectedEvent = 'all';

    function filterRows() {
        const groups = document.querySelectorAll('.day-group');

        groups.forEach(group => {
            const groupDate = group.dataset.date;

            const matchDay = selectedDay === 'all' || groupDate === selectedDay;

            if (!matchDay) {
                group.style.display = 'none';
                return;
            }

            const rows = group.querySelectorAll('.program-row');

            let visibleRows = 0;

            rows.forEach(row => {
                const event = row.dataset.event;

                const matchEvent = selectedEvent === 'all' || event === selectedEvent;

                row.style.display = matchEvent ? 'grid' : 'none';

                if (matchEvent) visibleRows++;
            });

            group.style.display = visibleRows > 0 ? 'block' : 'none';
        });
    }

    document.querySelectorAll('.filter-day').forEach(btn => {
        btn.addEventListener('click', () => {
            selectedDay = btn.dataset.day;

            document.querySelectorAll('.filter-day')
                .forEach(b => b.classList.remove('active'));

            btn.classList.add('active');

            filterRows();
        });
    });

    document.querySelectorAll('.filter-event').forEach(btn => {
        btn.addEventListener('click', () => {
            selectedEvent = btn.dataset.event;

            document.querySelectorAll('.filter-event')
                .forEach(b => b.classList.remove('active'));

            btn.classList.add('active');

            filterRows();
        });
    });

    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function () {

            const row = this.closest('.program-row');
            const sessionId = row.dataset.sessionId;

            fetch('/personal-program/delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ session_id: sessionId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {

                    const ticketEl = row.querySelector('.tickets');
                    let count = parseInt(ticketEl.textContent);

                    if (count > 1) {
                        ticketEl.textContent = count - 1;
                    } else {
                        row.remove();

                        filterRows();
                    }

                } else {
                    alert('Delete failed');
                }
            })
            .catch(() => alert('Server error'));
        });
    });
});