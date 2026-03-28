const danceDetailForm = document.querySelector('form[action*="/cms/events/dance-detail/"]');

async function uploadImage(row, button) {
    if (!window.CmsMediaUpload) {
        return;
    }

    const moduleName = row.dataset.imageUploadModule || (danceDetailForm ? danceDetailForm.dataset.imageUploadModule || '' : '');
    const fileInput = row.querySelector('.performer-upload-input');
    const sectionItemIdInput = row.querySelector('.performer-artist-item-id');
    const currentPathInput = row.querySelector('.performer-artist-image');

    const sectionItemId = sectionItemIdInput ? Number(sectionItemIdInput.value || 0) : 0;
    const path = await window.CmsMediaUpload.uploadImage({
        button,
        fileInput,
        moduleName,
        sectionItemId,
        currentPath: currentPathInput ? currentPathInput.value : '',
        missingSectionItemMessage: 'Missing hero image row. Please refresh the page and try again.',
        missingModuleMessage: 'Missing media upload module for this page.',
    });

    if (path) {
        window.CmsMediaUpload.applyUploadedPath(row, {
            pathInputSelector: '.performer-artist-image',
            downloadLinkSelector: '.performer-download-link',
        }, path);
    }
}

async function uploadAudio(row, button) {
    if (!window.CmsMediaUpload) {
        return;
    }

    const moduleName = row.dataset.audioUploadModule || '';
    const fileInput = row.querySelector('.performer-upload-audio-input');
    const sectionItemIdInput = row.querySelector('.performer-artist-item-id');
    const currentPathInput = row.querySelector('.performer-track-audio');

    const sectionItemId = sectionItemIdInput ? Number(sectionItemIdInput.value || 0) : 0;
    const path = await window.CmsMediaUpload.uploadAudio({
        button,
        fileInput,
        moduleName,
        sectionItemId,
        currentPath: currentPathInput ? currentPathInput.value : '',
        missingSectionItemMessage: 'Missing track row. Please refresh the page and try again.',
        missingModuleMessage: 'Missing media upload module for this track.',
    });

    if (path) {
        window.CmsMediaUpload.applyUploadedPath(row, {
            pathInputSelector: '.performer-track-audio',
            downloadLinkSelector: '.performer-audio-download-link',
        }, path);
    }
}

if (danceDetailForm) {
    danceDetailForm.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) {
            return;
        }

        if (target.classList.contains('upload-performer-image')) {
            const row = target.closest('[data-image-upload-module]');
            if (!row) {
                return;
            }

            uploadImage(row, target);
            return;
        }

        if (target.classList.contains('upload-performer-audio')) {
            const row = target.closest('[data-audio-upload-module]');
            if (!row) {
                return;
            }

            uploadAudio(row, target);
        }
    });
}

if (danceDetailForm && window.CmsPageEditor) {
    window.CmsPageEditor.initializeQuillEditors(danceDetailForm);
}

if (danceDetailForm && window.CmsFormSaveAPI) {
    window.CmsFormSaveAPI.initialize(danceDetailForm);
}
