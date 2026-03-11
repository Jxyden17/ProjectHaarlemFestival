const tourDetailsForm = document.querySelector('form[action="/cms/events/tour-details"]');
const uploadFeedback = window.CmsUploadFeedback || {
    showUploadFeedback() {},
    setUploadingState() {},
    resolveUploadErrorMessage(error, fallbackMessage) {
        return fallbackMessage;
    }
};

function applyUploadedTourImage(row, path) {
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

async function uploadTourImage(row, button) {
    if (!tourDetailsForm) {
        return;
    }

    const fileInput = row.querySelector('.performer-upload-input');
    const sectionItemIdInput = row.querySelector('.tour-item-id');
    const currentPathInput = row.querySelector('.performer-artist-image');
    const pageSlug = tourDetailsForm.dataset.tourPageSlug || '';
    const sectionType = row.dataset.tourSectionType || '';

    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        uploadFeedback.showUploadFeedback('Upload failed', 'Please choose an image first.', 'danger');
        return;
    }

    const sectionItemId = sectionItemIdInput ? Number(sectionItemIdInput.value || 0) : 0;
    if (sectionItemId <= 0 || pageSlug === '' || sectionType === '') {
        uploadFeedback.showUploadFeedback(
            'Upload failed',
            uploadFeedback.resolveUploadErrorMessage(new Error('Missing tour upload metadata for this image row.'), 'Image upload failed.'),
            'danger'
        );
        return;
    }

    const formData = new FormData();
    formData.append('image', fileInput.files[0]);
    formData.append('module', `tour_image:${pageSlug}:${sectionType}`);
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

        applyUploadedTourImage(row, payload.path);
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

if (tourDetailsForm) {
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

if (tourDetailsForm && window.CmsPageEditor) {
    window.CmsPageEditor.initializeQuillEditors(tourDetailsForm);
}
