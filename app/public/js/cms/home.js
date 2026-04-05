(function () {
    const homeForm = document.querySelector('form[action="/cms/events/home"]');

    async function uploadHomeImage(row, button) {
        if (!homeForm || !window.CmsMediaUpload) {
            return;
        }

        const fileInput = row.querySelector('.performer-upload-input');
        const sectionItemIdInput = row.querySelector('.home-item-id');
        const currentPathInput = row.querySelector('.home-image-path');
        const sectionType = row.dataset.homeSectionType || '';
        const moduleName = sectionType !== '' ? `home_image:${sectionType}` : '';

        const sectionItemId = sectionItemIdInput ? Number(sectionItemIdInput.value || 0) : 0;
        const path = await window.CmsMediaUpload.uploadImage({
            button,
            fileInput,
            moduleName,
            sectionItemId,
            currentPath: currentPathInput ? currentPathInput.value : '',
            missingSectionItemMessage: 'Missing Home upload metadata for this image row.',
            missingModuleMessage: 'Missing Home upload metadata for this image row.',
        });

        if (path) {
            window.CmsMediaUpload.applyUploadedPath(row, {
                pathInputSelector: '.home-image-path',
                downloadLinkSelector: '.performer-download-link',
            }, path);
        }
    }

    if (!homeForm) {
        return;
    }

    homeForm.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement) || !target.classList.contains('upload-performer-image')) {
            return;
        }

        const row = target.closest('[data-home-upload-row]');
        if (!row) {
            return;
        }

        uploadHomeImage(row, target);
    });
})();
