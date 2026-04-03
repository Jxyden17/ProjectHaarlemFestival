(function () {
    // Keep the upload endpoints and messages in one place for each media type.
    const MEDIA_TYPES = {
        image: {
            endpoint: '/cms/media/upload-image',
            fileFieldName: 'image',
            missingFileMessage: 'Please choose an image first.',
            failureMessage: 'Image upload failed.',
            successTitle: 'Upload complete',
            successMessage: 'Image uploaded successfully.',
        },
        audio: {
            endpoint: '/cms/media/upload-audio',
            fileFieldName: 'audio',
            missingFileMessage: 'Please choose an audio file first.',
            failureMessage: 'Audio upload failed.',
            successTitle: 'Upload complete',
            successMessage: 'Audio uploaded successfully.',
        },
    };

    // Provide a safe fallback when the shared feedback helper is not loaded yet.
    function getUploadFeedback() {
        return window.CmsUploadFeedback || {
            showUploadFeedback() {},
            setUploadingState() {},
            resolveUploadErrorMessage(error, fallbackMessage) {
                return fallbackMessage;
            }
        };
    }

    // Push the uploaded path back into the row so the current file can be reused immediately.
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

    // Post the selected media file and return the saved path when the upload succeeds.
    async function uploadMedia(type, options) {
        const uploadFeedback = getUploadFeedback();
        const typeConfig = MEDIA_TYPES[type];

        if (!typeConfig) {
            throw new Error(`Unsupported upload type: ${type}`);
        }

        const {
            button,
            fileInput,
            moduleName = '',
            sectionItemId = 0,
            currentPath = '',
            extraFields = null,
            missingSectionItemMessage = 'Missing media row. Please refresh the page and try again.',
            missingModuleMessage = 'Missing media upload module.',
        } = options;

        if (sectionItemId <= 0 || moduleName === '') {
            const metadataMessage = sectionItemId <= 0
                ? missingSectionItemMessage
                : missingModuleMessage;

            uploadFeedback.showUploadFeedback(
                'Upload failed',
                uploadFeedback.resolveUploadErrorMessage(new Error(metadataMessage), typeConfig.failureMessage),
                'danger'
            );
            return null;
        }

        if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
            uploadFeedback.showUploadFeedback('Upload failed', missingFileMessage, 'danger');
            return null;
        }

        const formData = new FormData();
        formData.append(typeConfig.fileFieldName, fileInput.files[0]);
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
            const response = await fetch(typeConfig.endpoint, {
                method: 'POST',
                body: formData
            });

            const payload = await response.json();
            if (!response.ok || !payload.success || !payload.path) {
                throw new Error(payload && payload.message ? payload.message : 'Upload failed');
            }

            uploadFeedback.showUploadFeedback(typeConfig.successTitle, typeConfig.successMessage, 'success');
            return payload.path;
        } catch (error) {
            uploadFeedback.showUploadFeedback(
                'Upload failed',
                uploadFeedback.resolveUploadErrorMessage(error, typeConfig.failureMessage),
                'danger'
            );
            return null;
        } finally {
            uploadFeedback.setUploadingState(button, false);
            fileInput.value = '';
        }
    }

    // Convenience wrapper for image uploads.
    async function uploadImage(options) {
        return uploadMedia('image', options);
    }

    // Convenience wrapper for audio uploads.
    async function uploadAudio(options) {
        return uploadMedia('audio', options);
    }

    window.CmsMediaUpload = {
        getUploadFeedback,
        applyUploadedPath,
        uploadImage,
        uploadAudio,
    };
})();
