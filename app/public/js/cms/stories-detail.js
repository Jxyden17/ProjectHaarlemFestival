(function () {
    const storiesDetailForm = document.querySelector('form[action="/cms/events/stories-details"]');

    async function uploadStoriesImage(row, button) {
        if (!storiesDetailForm || !window.CmsMediaUpload) {
            return;
        }

        const fileInput = row.querySelector('.performer-upload-input');
        const sectionItemIdInput = row.querySelector('.stories-item-id');
        const currentPathInput = row.querySelector('.stories-image-path');
        const pageSlug = storiesDetailForm.dataset.storiesPageSlug || '';
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

    async function uploadStoriesAudio(row, button) {
        if (!storiesDetailForm || !window.CmsMediaUpload) {
            return;
        }

        const fileInput = row.querySelector('.performer-upload-audio-input');
        const sectionItemIdInput = row.querySelector('.stories-item-id');
        const currentPathInput = row.querySelector('.stories-audio-path');
        const pageSlug = storiesDetailForm.dataset.storiesPageSlug || '';

        if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
            window.CmsMediaUpload.getUploadFeedback().showUploadFeedback('Upload failed', 'Please choose an audio file first.', 'danger');
            return;
        }

        const sectionItemId = sectionItemIdInput ? Number(sectionItemIdInput.value || 0) : 0;
        if (sectionItemId <= 0 || pageSlug === '') {
            window.CmsMediaUpload.getUploadFeedback().showUploadFeedback('Upload failed', 'Audio upload failed.', 'danger');
            return;
        }

        const path = await window.CmsMediaUpload.uploadFile({
            button,
            fileInput,
            endpoint: '/cms/media/upload-audio',
            fileFieldName: 'audio',
            moduleName: `stories_detail_audio:${pageSlug}`,
            sectionItemId,
            currentPath: currentPathInput ? currentPathInput.value : '',
            missingMetadataMessage: 'Missing Stories audio upload metadata for this row.',
            missingFileMessage: 'Please choose an audio file first.',
            failureMessage: 'Audio upload failed.',
            successTitle: 'Upload complete',
            successMessage: 'Audio uploaded successfully.',
        });

        if (path) {
            window.CmsMediaUpload.applyUploadedPath(row, {
                pathInputSelector: '.stories-audio-path',
                downloadLinkSelector: '.performer-audio-download-link',
            }, path);
        }
    }

    if (storiesDetailForm) {
        storiesDetailForm.addEventListener('click', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement)) {
                return;
            }

            if (target.classList.contains('upload-performer-image')) {
                const row = target.closest('[data-stories-upload-row]');
                if (!row) {
                    return;
                }

                uploadStoriesImage(row, target);
                return;
            }

            if (target.classList.contains('upload-performer-audio')) {
                const row = target.closest('[data-stories-audio-upload-row]');
                if (!row) {
                    return;
                }

                uploadStoriesAudio(row, target);
            }
        });
    }
})();
