(() => {
    const buttons = Array.from(document.querySelectorAll('.dance-detail-track-play[data-audio-src]'));
    if (buttons.length === 0) {
        return;
    }

    const audio = new Audio();
    let activeButton = null;

    const refreshIcons = () => {
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    };

    const setButtonState = (button, state) => {
        if (!button) {
            return;
        }

        button.dataset.state = state;
        button.innerHTML = state === 'playing'
            ? '<i data-lucide="pause" aria-hidden="true"></i>'
            : '<i data-lucide="play" aria-hidden="true"></i>';
        refreshIcons();
    };

    const stopActive = () => {
        if (activeButton) {
            setButtonState(activeButton, 'paused');
            activeButton = null;
        }
        audio.pause();
        audio.currentTime = 0;
    };

    audio.addEventListener('ended', stopActive);
    audio.addEventListener('error', stopActive);

    buttons.forEach((button) => {
        button.addEventListener('click', async () => {
            const src = (button.dataset.audioSrc || '').trim();
            if (src === '') {
                return;
            }

            if (button === activeButton && !audio.paused) {
                stopActive();
                return;
            }

            if (activeButton && activeButton !== button) {
                setButtonState(activeButton, 'paused');
            }

            if (audio.src !== new URL(src, window.location.origin).href) {
                audio.src = src;
            }

            try {
                await audio.play();
                activeButton = button;
                setButtonState(button, 'playing');
            } catch (e) {
                setButtonState(button, 'paused');
            }
        });
    });
})();
