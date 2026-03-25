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
    const moduleName = pageSlug !== '' && sectionType !== '' ? `tour_image:${pageSlug}:${sectionType}` : '';

    const sectionItemId = sectionItemIdInput ? Number(sectionItemIdInput.value || 0) : 0;
    const path = await window.CmsMediaUpload.uploadImage({
        button,
        fileInput,
        moduleName,
        sectionItemId,
        currentPath: currentPathInput ? currentPathInput.value : '',
        missingSectionItemMessage: 'Missing tour upload metadata for this image row.',
        missingModuleMessage: 'Missing tour upload metadata for this image row.',
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
