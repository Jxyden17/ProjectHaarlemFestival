document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form[action*="yummy-home"]');

    if (!form) {
        console.warn('Yummy form not found');
        return;
    }

    // Delegate image upload clicks for every editable Yummy home row.
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

// Upload the selected image and persist its returned path on the home row.
async function uploadYummyImage(row, button, form) {
    if (!window.CmsMediaUpload) return;

    const fileInput = row.querySelector('.yummy-image-input');
    const sectionItemIdInput = row.querySelector('.yummy-item-id');
    const currentPathInput = row.querySelector('.yummy-image-path');

    const pageSlug = form.dataset.yummyPageSlug || '';
    const sectionType = row.dataset.yummySectionType || '';

    const sectionItemId = sectionItemIdInput ? Number(sectionItemIdInput.value || 0) : 0;
    const moduleName = pageSlug === '' || sectionType === ''
        ? ''
        : `yummy:${pageSlug}:${sectionType}`;

    const path = await window.CmsMediaUpload.uploadImage({
        button,
        fileInput,
        moduleName,
        sectionItemId,
        currentPath: currentPathInput ? currentPathInput.value : '',
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
