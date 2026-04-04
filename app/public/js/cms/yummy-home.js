document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form[action*="yummy-home"]');

    if (!form) {
        console.warn('Yummy form not found');
        return;
    }

    form.addEventListener('click', (event) => {
        const target = event.target;

        if (!(target instanceof HTMLElement) || !target.classList.contains('upload-yummy-image')) {
            return;
        }

        const row = target.closest('[data-yummy-upload-row]');
        if (!row) {
            console.warn('Upload row not found');
            return;
        }

        uploadYummyImage(row, target, form);
    });

    if (window.CmsPageEditor) {
        window.CmsPageEditor.initializeQuillEditors(form);
    }
});

async function uploadYummyImage(row, button, form) {
    if (!window.CmsMediaUpload) return;

    const fileInput = row.querySelector('.yummy-image-input');
    const sectionItemIdInput = row.querySelector('.yummy-item-id');
    const currentPathInput = row.querySelector('.yummy-image-path');

    const pageSlug = form.dataset.yummyPageSlug || '';
    const sectionType = row.dataset.yummySectionType || '';

    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        window.CmsMediaUpload
            .getUploadFeedback()
            .showUploadFeedback('Upload failed', 'Please choose an image first.', 'danger');
        return;
    }

    const sectionItemId = sectionItemIdInput ? Number(sectionItemIdInput.value || 0) : 0;

    if (sectionItemId <= 0 || pageSlug === '' || sectionType === '') {
        window.CmsMediaUpload
            .getUploadFeedback()
            .showUploadFeedback('Upload failed', 'Missing upload metadata.', 'danger');
        return;
    }

    const path = await window.CmsMediaUpload.uploadFile({
        button,
        fileInput,
        endpoint: '/cms/media/upload-image',
        fileFieldName: 'image',
        moduleName: `yummy`,

        sectionItemId,
        currentPath: currentPathInput ? currentPathInput.value : '',

        missingMetadataMessage: 'Missing upload metadata.',
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