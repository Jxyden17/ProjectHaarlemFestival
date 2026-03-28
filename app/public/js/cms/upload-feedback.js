(function () {
    const uploadFeedbackModalElement = document.getElementById('cmsUploadFeedbackModal');
    const uploadFeedbackTitle = document.getElementById('cmsUploadFeedbackModalLabel');
    const uploadFeedbackAlert = document.getElementById('cmsUploadFeedbackAlert');
    const uploadFeedbackMessage = document.getElementById('cmsUploadFeedbackMessage');
    let uploadFeedbackModal = null;

    function getUploadFeedbackModal() {
        if (!uploadFeedbackModalElement || typeof bootstrap === 'undefined') {
            return null;
        }

        if (uploadFeedbackModal === null) {
            uploadFeedbackModal = new bootstrap.Modal(uploadFeedbackModalElement);
        }

        return uploadFeedbackModal;
    }

    function showUploadFeedback(title, message, variant) {
        const modalInstance = getUploadFeedbackModal();
        if (!modalInstance || !uploadFeedbackTitle || !uploadFeedbackAlert || !uploadFeedbackMessage) {
            return;
        }

        uploadFeedbackTitle.textContent = title;
        uploadFeedbackMessage.textContent = message;
        uploadFeedbackAlert.className = `alert mb-0 alert-${variant}`;
        modalInstance.show();
    }

    function setUploadingState(button, isUploading) {
        if (!(button instanceof HTMLElement)) {
            return;
        }

        if (!button.dataset.originalLabel) {
            button.dataset.originalLabel = button.textContent || 'Upload';
        }

        button.disabled = isUploading;
        button.textContent = isUploading ? 'Uploading...' : button.dataset.originalLabel;
    }

    function resolveUploadErrorMessage(error, fallbackMessage) {
        const debugEnabled = uploadFeedbackModalElement
            ? uploadFeedbackModalElement.dataset.debugEnabled === '1'
            : false;

        if (!debugEnabled) {
            return fallbackMessage;
        }

        if (error && typeof error.message === 'string' && error.message.trim() !== '') {
            return error.message.trim();
        }

        return fallbackMessage;
    }

    window.CmsUploadFeedback = {
        showUploadFeedback,
        setUploadingState,
        resolveUploadErrorMessage,
    };
})();
