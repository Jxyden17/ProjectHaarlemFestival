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

        const sectionItemId = sectionItemIdInput ? Number(sectionItemIdInput.value || 0) : 0;
        const moduleName = pageSlug === '' || sectionType === ''
            ? ''
            : `stories_home:${pageSlug}:${sectionType}`;

        const path = await window.CmsMediaUpload.uploadImage({
            button,
            fileInput,
            moduleName,
            sectionItemId,
            currentPath: currentPathInput ? currentPathInput.value : '',
            missingSectionItemMessage: 'Missing Stories image row. Please refresh the page and try again.',
            missingModuleMessage: 'Missing Stories upload metadata for this image row.',
            missingFileMessage: 'Please choose an image first.',
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

        const sectionItemId = sectionItemIdInput ? Number(sectionItemIdInput.value || 0) : 0;
        const moduleName = pageSlug === '' ? '' : `stories_detail_audio:${pageSlug}`;

        const path = await window.CmsMediaUpload.uploadAudio({
            button,
            fileInput,
            moduleName,
            sectionItemId,
            currentPath: currentPathInput ? currentPathInput.value : '',
            missingSectionItemMessage: 'Missing Stories audio row. Please refresh the page and try again.',
            missingModuleMessage: 'Missing Stories audio upload metadata for this row.',
            missingFileMessage: 'Please choose an audio file first.',
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

    if (storiesDetailForm && window.CmsFormSaveAPI) {
        window.CmsFormSaveAPI.initialize(storiesDetailForm);
    }
})();
