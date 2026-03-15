(function () {
    function getUploadFeedback() {
        return window.CmsUploadFeedback || {
            showUploadFeedback() {},
            setUploadingState() {},
            resolveUploadErrorMessage(error, fallbackMessage) {
                return fallbackMessage;
            }
        };
    }

    function applyUploadedPath(row, options, path) {
        if (!(row instanceof Element)) {
            return;
        }

        const pathInput = row.querySelector(options.pathInputSelector);
        const downloadLink = row.querySelector(options.downloadLinkSelector);

        if (pathInput) {
            pathInput.value = path;
        }

        if (downloadLink) {
            downloadLink.href = path;
            downloadLink.classList.remove('d-none');
        }
    }

    async function uploadFile(options) {
        const uploadFeedback = getUploadFeedback();
        const {
            button,
            fileInput,
            endpoint,
            fileFieldName,
            moduleName,
            sectionItemId,
            currentPath,
            extraFields,
            missingMetadataMessage,
            missingFileMessage,
            failureMessage,
            successTitle,
            successMessage,
        } = options;

        if (moduleName === '' || sectionItemId <= 0) {
            uploadFeedback.showUploadFeedback(
                'Upload failed',
                uploadFeedback.resolveUploadErrorMessage(new Error(missingMetadataMessage), failureMessage),
                'danger'
            );
            return null;
        }

        if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
            uploadFeedback.showUploadFeedback('Upload failed', missingFileMessage, 'danger');
            return null;
        }

        const formData = new FormData();
        formData.append(fileFieldName, fileInput.files[0]);
        formData.append('module', moduleName);
        formData.append('section_item_id', String(sectionItemId));
        formData.append('current_path', currentPath);

        if (extraFields && typeof extraFields === 'object') {
            Object.entries(extraFields).forEach(([key, value]) => {
                formData.append(key, String(value));
            });
        }

        uploadFeedback.setUploadingState(button, true);

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                body: formData
            });

            const payload = await response.json();
            if (!response.ok || !payload.success || !payload.path) {
                throw new Error(payload && payload.message ? payload.message : 'Upload failed');
            }

            uploadFeedback.showUploadFeedback(successTitle, successMessage, 'success');
            return payload.path;
        } catch (error) {
            uploadFeedback.showUploadFeedback(
                'Upload failed',
                uploadFeedback.resolveUploadErrorMessage(error, failureMessage),
                'danger'
            );
            return null;
        } finally {
            uploadFeedback.setUploadingState(button, false);
            fileInput.value = '';
        }
    }

    window.CmsMediaUpload = {
        getUploadFeedback,
        applyUploadedPath,
        uploadFile,
    };
})();
