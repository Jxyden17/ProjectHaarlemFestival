const danceDetailForm = document.querySelector('form[action*="/cms/events/dance-detail/"]');
const uploadFeedback = window.CmsUploadFeedback || {
    showUploadFeedback() {},
    setUploadingState() {},
    resolveUploadErrorMessage(error, fallbackMessage) {
        return fallbackMessage;
    }
};

function applyUploadedImage(row, path) {
    const imagePathInput = row.querySelector('.performer-artist-image');
    const downloadLink = row.querySelector('.performer-download-link');

    if (imagePathInput) {
        imagePathInput.value = path;
    }

    if (downloadLink) {
        downloadLink.href = path;
        downloadLink.classList.remove('d-none');
    }
}

function applyUploadedAudio(row, path) {
    const audioPathInput = row.querySelector('.performer-track-audio');
    const downloadLink = row.querySelector('.performer-audio-download-link');

    if (audioPathInput) {
        audioPathInput.value = path;
    }

    if (downloadLink) {
        downloadLink.href = path;
        downloadLink.classList.remove('d-none');
    }
}

async function uploadImage(row, button) {
    const moduleName = row.dataset.imageUploadModule || (danceDetailForm ? danceDetailForm.dataset.imageUploadModule || '' : '');
    const fileInput = row.querySelector('.performer-upload-input');
    const sectionItemIdInput = row.querySelector('.performer-artist-item-id');
    const currentPathInput = row.querySelector('.performer-artist-image');

    if (moduleName === '') {
        uploadFeedback.showUploadFeedback(
            'Upload failed',
            uploadFeedback.resolveUploadErrorMessage(new Error('Missing media upload module for this page.'), 'Image upload failed.'),
            'danger'
        );
        return;
    }

    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        uploadFeedback.showUploadFeedback('Upload failed', 'Please choose an image first.', 'danger');
        return;
    }

    const sectionItemId = sectionItemIdInput ? Number(sectionItemIdInput.value || 0) : 0;
    if (sectionItemId <= 0) {
        uploadFeedback.showUploadFeedback(
            'Upload failed',
            uploadFeedback.resolveUploadErrorMessage(new Error('Missing hero image row. Please refresh the page and try again.'), 'Image upload failed.'),
            'danger'
        );
        return;
    }

    const formData = new FormData();
    formData.append('image', fileInput.files[0]);
    formData.append('module', moduleName);
    formData.append('section_item_id', String(sectionItemId));
    formData.append('current_path', currentPathInput ? currentPathInput.value : '');

    uploadFeedback.setUploadingState(button, true);
    try {
        const response = await fetch('/cms/media/upload-replace', {
            method: 'POST',
            body: formData
        });

        const payload = await response.json();
        if (!response.ok || !payload.success || !payload.path) {
            throw new Error(payload && payload.message ? payload.message : 'Upload failed');
        }

        applyUploadedImage(row, payload.path);
        uploadFeedback.showUploadFeedback('Upload complete', 'Image uploaded successfully.', 'success');
    } catch (error) {
        uploadFeedback.showUploadFeedback(
            'Upload failed',
            uploadFeedback.resolveUploadErrorMessage(error, 'Image upload failed.'),
            'danger'
        );
    } finally {
        uploadFeedback.setUploadingState(button, false);
        fileInput.value = '';
    }
}

async function uploadAudio(row, button) {
    const moduleName = row.dataset.audioUploadModule || '';
    const fileInput = row.querySelector('.performer-upload-audio-input');
    const sectionItemIdInput = row.querySelector('.performer-artist-item-id');
    const currentPathInput = row.querySelector('.performer-track-audio');

    if (moduleName === '') {
        uploadFeedback.showUploadFeedback(
            'Upload failed',
            uploadFeedback.resolveUploadErrorMessage(new Error('Missing media upload module for this track.'), 'Audio upload failed.'),
            'danger'
        );
        return;
    }

    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        uploadFeedback.showUploadFeedback('Upload failed', 'Please choose an audio file first.', 'danger');
        return;
    }

    const sectionItemId = sectionItemIdInput ? Number(sectionItemIdInput.value || 0) : 0;
    if (sectionItemId <= 0) {
        uploadFeedback.showUploadFeedback(
            'Upload failed',
            uploadFeedback.resolveUploadErrorMessage(new Error('Missing track row. Please refresh the page and try again.'), 'Audio upload failed.'),
            'danger'
        );
        return;
    }

    const formData = new FormData();
    formData.append('audio', fileInput.files[0]);
    formData.append('module', moduleName);
    formData.append('section_item_id', String(sectionItemId));
    formData.append('current_path', currentPathInput ? currentPathInput.value : '');

    uploadFeedback.setUploadingState(button, true);
    try {
        const response = await fetch('/cms/media/upload-audio', {
            method: 'POST',
            body: formData
        });

        const payload = await response.json();
        if (!response.ok || !payload.success || !payload.path) {
            throw new Error(payload && payload.message ? payload.message : 'Upload failed');
        }

        applyUploadedAudio(row, payload.path);
        uploadFeedback.showUploadFeedback('Upload complete', 'Audio uploaded successfully.', 'success');
    } catch (error) {
        uploadFeedback.showUploadFeedback(
            'Upload failed',
            uploadFeedback.resolveUploadErrorMessage(error, 'Audio upload failed.'),
            'danger'
        );
    } finally {
        uploadFeedback.setUploadingState(button, false);
        fileInput.value = '';
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
