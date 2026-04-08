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

    // Delegate image upload clicks for every editable Yummy detail row.
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

// Upload the selected image and persist its returned path on the detail row.
async function uploadYummyImage(row, button, form) {

    if (!window.CmsMediaUpload) {
        console.warn('CmsMediaUpload not available');
        return;
    }

    const fileInput = row.querySelector('.yummy-image-input');
    const sectionItemIdInput = row.querySelector('.yummy-item-id');
    const currentPathInput = row.querySelector('.yummy-image-path');

    const sectionItemId = Number(sectionItemIdInput?.value || 0);

    const moduleName =
        row.dataset.imageUploadModule ||
        form.dataset.imageUploadModule ||
        '';

    const path = await window.CmsMediaUpload.uploadImage({
        button,
        fileInput,
        moduleName,
        sectionItemId,
        currentPath: currentPathInput?.value || '',
        missingSectionItemMessage: 'Missing Yummy image row. Please refresh the page and try again.',
        missingModuleMessage: 'Missing Yummy upload metadata.',
        missingFileMessage: 'Please choose an image first.',
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
