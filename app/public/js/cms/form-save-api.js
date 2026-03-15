(function () {
    function showFeedback(title, message, isSuccess) {
        if (!window.CmsUploadFeedback) {
            return;
        }

        window.CmsUploadFeedback.showUploadFeedback(
            title,
            message,
            isSuccess ? 'success' : 'danger'
        );
    }

    function setSubmittingState(button, isSubmitting) {
        if (!(button instanceof HTMLButtonElement)) {
            return;
        }

        if (!button.dataset.originalLabel) {
            button.dataset.originalLabel = button.textContent || 'Save';
        }

        button.disabled = isSubmitting;
        button.textContent = isSubmitting ? 'Saving...' : button.dataset.originalLabel;
    }

    function initialize(form) {
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        if (form.dataset.saveApiInitialized === '1') {
            return;
        }

        const saveApiUrl = form.dataset.saveApi || '';
        if (saveApiUrl === '') {
            return;
        }

        form.dataset.saveApiInitialized = '1';

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const submitButton = form.querySelector('button[type="submit"]');
            const formData = new FormData(form);

            setSubmittingState(submitButton, true);

            try {
                const response = await fetch(saveApiUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const payload = await response.json();
                const message = payload && typeof payload.message === 'string'
                    ? payload.message
                    : 'Saving failed.';

                showFeedback(
                    response.ok && !!payload.success ? 'Save complete' : 'Save failed',
                    message,
                    response.ok && !!payload.success
                );
            } catch (_error) {
                showFeedback('Save failed', 'Saving failed.', false);
            } finally {
                setSubmittingState(submitButton, false);
            }
        });
    }

    function initializeAll(root) {
        const scope = root instanceof ParentNode ? root : document;
        const forms = scope.querySelectorAll('form[data-save-api]');
        forms.forEach((form) => {
            initialize(form);
        });
    }

    window.CmsFormSaveAPI = {
        initialize,
        initializeAll,
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => initializeAll(), { once: true });
    } else {
        initializeAll();
    }
})();
