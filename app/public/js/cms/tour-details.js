const tourDetailsForm = document.querySelector('form[action="/cms/events/tour-details"]');

// Upload the selected image for a specific tour detail content row.
async function uploadTourImage(row, button) {
    if (!tourDetailsForm || !window.CmsMediaUpload) {
        return;
    }

    const fileInput = row.querySelector('.performer-upload-input');
    const sectionItemIdInput = row.querySelector('.tour-item-id');
    const currentPathInput = row.querySelector('.performer-artist-image');
    const pageSlug = tourDetailsForm.dataset.tourPageSlug || '';
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

if (tourDetailsForm) {
    // Delegate upload clicks so all tour detail image rows share one listener.
    tourDetailsForm.addEventListener('click', (event) => {
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

// Upgrade rich text fields on the tour detail editor.
if (tourDetailsForm && window.CmsPageEditor) {
    window.CmsPageEditor.initializeQuillEditors(tourDetailsForm);
}
