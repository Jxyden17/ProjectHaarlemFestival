const tourHomeForm = document.querySelector('form[action="/cms/events/tour-home"]');

async function uploadTourImage(row, button) {
    if (!tourHomeForm || !window.CmsMediaUpload) {
        return;
    }

    const fileInput = row.querySelector('.performer-upload-input');
    const sectionItemIdInput = row.querySelector('.tour-item-id');
    const currentPathInput = row.querySelector('.performer-artist-image');
    const pageSlug = tourHomeForm.dataset.tourPageSlug || '';
    const sectionType = row.dataset.tourSectionType || '';

    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        window.CmsMediaUpload.getUploadFeedback().showUploadFeedback('Upload failed', 'Please choose an image first.', 'danger');
        return;
    }

    const sectionItemId = sectionItemIdInput ? Number(sectionItemIdInput.value || 0) : 0;
    if (sectionItemId <= 0 || pageSlug === '' || sectionType === '') {
        window.CmsMediaUpload.getUploadFeedback().showUploadFeedback('Upload failed', 'Image upload failed.', 'danger');
        return;
    }

    const path = await window.CmsMediaUpload.uploadFile({
        button,
        fileInput,
        endpoint: '/cms/media/upload-image',
        fileFieldName: 'image',
        moduleName: `tour_image:${pageSlug}:${sectionType}`,
        sectionItemId,
        currentPath: currentPathInput ? currentPathInput.value : '',
        missingMetadataMessage: 'Missing tour upload metadata for this image row.',
        missingFileMessage: 'Please choose an image first.',
        failureMessage: 'Image upload failed.',
        successTitle: 'Upload complete',
        successMessage: 'Image uploaded successfully.',
    });

    if (path) {
        window.CmsMediaUpload.applyUploadedPath(row, {
            pathInputSelector: '.performer-artist-image',
            downloadLinkSelector: '.performer-download-link',
        }, path);
    }
}

if (tourHomeForm) {
    tourHomeForm.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement) || !target.classList.contains('upload-performer-image')) {
            return;
        }

        const row = target.closest('[data-tour-upload-row]');
        if (!row) {
            return;
        }

        uploadTourImage(row, target);
    });
}

if (tourHomeForm && window.CmsPageEditor) {
    window.CmsPageEditor.initializeQuillEditors(tourHomeForm);
}
