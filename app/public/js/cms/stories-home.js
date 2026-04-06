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
