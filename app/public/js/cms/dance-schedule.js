const scheduleForm = document.querySelector('form[action*="/cms/events/"][action$="/schedule"]');

async function uploadPerformerImage(row, button) {
    if (!window.CmsMediaUpload) {
        return;
    }

    const fileInput = row.querySelector('.performer-upload-input');
    const sectionItemIdInput = row.querySelector('.performer-artist-item-id');
    const currentPathInput = row.querySelector('.performer-artist-image');

    const sectionItemId = sectionItemIdInput ? Number(sectionItemIdInput.value || 0) : 0;
    const path = await window.CmsMediaUpload.uploadFile({
        button,
        fileInput,
        endpoint: '/cms/media/upload-replace',
        fileFieldName: 'image',
        moduleName: 'dance_artist',
        sectionItemId,
        currentPath: currentPathInput ? currentPathInput.value : '',
        missingMetadataMessage: 'Missing artist image row. Please configure artist images in dance content first.',
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

if (scheduleForm) {
    scheduleForm.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement) || !target.classList.contains('upload-performer-image')) {
            return;
        }

        const row = target.closest('tr');
        if (!row) {
            return;
        }

        uploadPerformerImage(row, target);
    });
}
