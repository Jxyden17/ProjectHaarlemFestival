document.addEventListener('DOMContentLoaded', () => {

    if (window.YummyDetailsInitialized) return;
    window.YummyDetailsInitialized = true;

    const form = document.querySelector('form[action*="yummy-details"]');
    if (!form) return;

    if (window.CmsPageEditor) {
        window.CmsPageEditor.initializeQuillEditors(form);
    }

    if (window.CmsFormSaveAPI) {
        window.CmsFormSaveAPI.initialize(form);
    }

    form.addEventListener('click', (event) => {

        const target = event.target;

        if (!(target instanceof HTMLElement)) return;
        if (!target.classList.contains('upload-yummy-image')) return;

        const row = target.closest('[data-image-upload-module]');
        if (!row) {
            console.warn('Upload row not found');
            return;
        }

        uploadYummyImage(row, target, form);
    });

});

async function uploadYummyImage(row, button, form) {

    if (!window.CmsMediaUpload) {
        console.warn('CmsMediaUpload not available');
        return;
    }

    const fileInput = row.querySelector('.yummy-image-input');
    const sectionItemIdInput = row.querySelector('.yummy-item-id');
    const currentPathInput = row.querySelector('.yummy-image-path');

    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        window.CmsMediaUpload
            .getUploadFeedback()
            .showUploadFeedback(
                'Upload failed',
                'Please choose an image first.',
                'danger'
            );
        return;
    }

    const sectionItemId = Number(sectionItemIdInput?.value || 0);

    const moduleName =
        row.dataset.imageUploadModule ||
        form.dataset.imageUploadModule ||
        '';

    if (!moduleName || sectionItemId <= 0) {
        window.CmsMediaUpload
            .getUploadFeedback()
            .showUploadFeedback(
                'Upload failed',
                'Missing upload metadata.',
                'danger'
            );
        return;
    }

    console.log('Uploading yummy detail image', {
        moduleName,
        sectionItemId
    });

    const path = await window.CmsMediaUpload.uploadFile({
        button,
        fileInput,
        endpoint: '/cms/media/upload-image',
        fileFieldName: 'image',

        moduleName,
        sectionItemId,
        currentPath: currentPathInput?.value || '',

        missingFileMessage: 'Please choose an image first.',
        failureMessage: 'Image upload failed.',
        successTitle: 'Upload complete',
        successMessage: 'Image uploaded successfully.',
    });

    if (path) {
        window.CmsMediaUpload.applyUploadedPath(
            row,
            {
                pathInputSelector: '.yummy-image-path',
                downloadLinkSelector: '.yummy-image-download-link',
            },
            path
        );
    }
}