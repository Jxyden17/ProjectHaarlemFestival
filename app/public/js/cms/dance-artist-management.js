(function () {
    const managementPage = document.querySelector('.dance-artist-management');

    // Upload the selected artist image and refresh the hidden path fields in the row.
    async function uploadPerformerImage(row, button) {
        if (!window.CmsMediaUpload) {
            return;
        }

        const fileInput = row.querySelector('.performer-upload-input');
        const sectionItemIdInput = row.querySelector('.performer-artist-item-id');
        const currentPathInput = row.querySelector('.performer-artist-image');
        const sectionItemId = sectionItemIdInput ? Number(sectionItemIdInput.value || 0) : 0;

        const path = await window.CmsMediaUpload.uploadImage({
            button,
            fileInput,
            moduleName: 'dance_artist',
            sectionItemId,
            currentPath: currentPathInput ? currentPathInput.value : '',
            missingSectionItemMessage: 'Missing artist image row. Please configure artist images in dance content first.',
        });

        if (path) {
            window.CmsMediaUpload.applyUploadedPath(row, {
                pathInputSelector: '.performer-artist-image',
                downloadLinkSelector: '.performer-download-link',
            }, path);
        }
    }

    if (!managementPage) {
        return;
    }

    // Delegate upload clicks so each table row can reuse the same handler.
    managementPage.addEventListener('click', (event) => {
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
})();
