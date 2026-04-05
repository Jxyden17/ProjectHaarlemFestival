(function () {
    const storiesHomeForm = document.querySelector('form[action="/cms/events/stories-home"]');

    async function uploadStoriesImage(row, button) {
        if (!storiesHomeForm || !window.CmsMediaUpload) {
            return;
        }

        const fileInput = row.querySelector('.performer-upload-input');
        const sectionItemIdInput = row.querySelector('.stories-item-id');
        const currentPathInput = row.querySelector('.stories-image-path');
        const pageSlug = storiesHomeForm.dataset.storiesPageSlug || 'stories';
        const sectionType = row.dataset.storiesSectionType || '';

        if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
            window.CmsMediaUpload.getUploadFeedback().showUploadFeedback('Upload failed', 'Please choose an image first.', 'danger');
            return;
        }

        const sectionItemId = sectionItemIdInput ? Number(sectionItemIdInput.value || 0) : 0;
        if (sectionItemId <= 0 || pageSlug === '' || sectionType === '') {
            window.CmsMediaUpload.getUploadFeedback().showUploadFeedback('Upload failed', 'Image upload failed.', 'danger');
            return;
        }

        const path = await window.CmsMediaUpload.uploadFile({
            button,
            fileInput,
            endpoint: '/cms/media/upload-image',
            fileFieldName: 'image',
            moduleName: `stories_home:${pageSlug}:${sectionType}`,
            sectionItemId,
            currentPath: currentPathInput ? currentPathInput.value : '',
            missingMetadataMessage: 'Missing Stories upload metadata for this image row.',
            missingFileMessage: 'Please choose an image first.',
            failureMessage: 'Image upload failed.',
            successTitle: 'Upload complete',
            successMessage: 'Image uploaded successfully.',
        });

        if (path) {
            window.CmsMediaUpload.applyUploadedPath(row, {
                pathInputSelector: '.stories-image-path',
                downloadLinkSelector: '.performer-download-link',
            }, path);
        }
    }

    if (storiesHomeForm) {
        storiesHomeForm.addEventListener('click', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement) || !target.classList.contains('upload-performer-image')) {
                return;
            }

            const row = target.closest('[data-stories-upload-row]');
            if (!row) {
                return;
            }

            uploadStoriesImage(row, target);
        });
    }

})();
