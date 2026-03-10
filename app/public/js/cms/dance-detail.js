const danceDetailForm = document.querySelector('form[action*="/cms/events/dance-detail/"]');
const quillEditors = [];

function initializeQuillEditors() {
    if (!danceDetailForm || typeof Quill === 'undefined') {
        return;
    }

    const toolbarOptions = [
        [{ header: [2, 3, 4, false] }],
        ['bold', 'italic', 'underline'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['link', 'blockquote'],
        ['clean']
    ];

    const richTextAreas = danceDetailForm.querySelectorAll('textarea[data-quill="1"]');
    richTextAreas.forEach((textarea) => {
        const editorContainer = document.createElement('div');
        editorContainer.className = 'mb-2';
        textarea.insertAdjacentElement('afterend', editorContainer);

        const quill = new Quill(editorContainer, {
            theme: 'snow',
            modules: {
                toolbar: toolbarOptions
            }
        });

        const initialHtml = textarea.value.trim();
        if (initialHtml === '') {
            quill.setText('');
        } else {
            quill.clipboard.dangerouslyPasteHTML(initialHtml);
        }

        textarea.required = false;
        textarea.classList.add('d-none');
        quillEditors.push({ textarea, quill });
    });

    danceDetailForm.addEventListener('submit', () => {
        quillEditors.forEach(({ textarea, quill }) => {
            const isEmpty = quill.getText().trim() === '';
            textarea.value = isEmpty ? '' : quill.root.innerHTML;
        });
    });
}

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
        alert('Missing media upload module for this page.');
        return;
    }

    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        alert('Please choose an image first.');
        return;
    }

    const sectionItemId = sectionItemIdInput ? Number(sectionItemIdInput.value || 0) : 0;
    if (sectionItemId <= 0) {
        alert('Missing hero image row. Please refresh the page and try again.');
        return;
    }

    const formData = new FormData();
    formData.append('image', fileInput.files[0]);
    formData.append('module', moduleName);
    formData.append('section_item_id', String(sectionItemId));
    formData.append('current_path', currentPathInput ? currentPathInput.value : '');

    button.disabled = true;
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
    } catch (error) {
        alert(error && error.message ? error.message : 'Image upload failed.');
    } finally {
        button.disabled = false;
        fileInput.value = '';
    }
}

async function uploadAudio(row, button) {
    const moduleName = row.dataset.audioUploadModule || '';
    const fileInput = row.querySelector('.performer-upload-audio-input');
    const sectionItemIdInput = row.querySelector('.performer-artist-item-id');
    const currentPathInput = row.querySelector('.performer-track-audio');

    if (moduleName === '') {
        alert('Missing media upload module for this track.');
        return;
    }

    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        alert('Please choose an audio file first.');
        return;
    }

    const sectionItemId = sectionItemIdInput ? Number(sectionItemIdInput.value || 0) : 0;
    if (sectionItemId <= 0) {
        alert('Missing track row. Please refresh the page and try again.');
        return;
    }

    const formData = new FormData();
    formData.append('audio', fileInput.files[0]);
    formData.append('module', moduleName);
    formData.append('section_item_id', String(sectionItemId));
    formData.append('current_path', currentPathInput ? currentPathInput.value : '');

    button.disabled = true;
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
    } catch (error) {
        alert(error && error.message ? error.message : 'Audio upload failed.');
    } finally {
        button.disabled = false;
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

initializeQuillEditors();
