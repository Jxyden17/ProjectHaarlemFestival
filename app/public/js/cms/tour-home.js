const tourHomeForm = document.querySelector('form[action="/cms/events/tour-home"]');
const quillEditors = [];

function initializeQuillEditors() {
    if (!tourHomeForm || typeof Quill === 'undefined') {
        return;
    }

    const toolbarOptions = [
        [{ header: [2, 3, 4, false] }],
        ['bold', 'italic', 'underline'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['link', 'blockquote'],
        ['clean']
    ];

    const richTextAreas = tourHomeForm.querySelectorAll('textarea[data-quill="1"]');
    richTextAreas.forEach((textarea) => {
        const editorContainer = document.createElement('div');
        editorContainer.className = 'mb-2';
        textarea.insertAdjacentElement('afterend', editorContainer);

        const quill = new Quill(editorContainer, {
            theme: 'snow',
            modules: { toolbar: toolbarOptions }
        });

        const initialHtml = textarea.value.trim();
        quill.root.innerHTML = initialHtml === '' ? '<p><br></p>' : initialHtml;
        textarea.required = false;
        textarea.classList.add('d-none');
        quillEditors.push({ textarea, quill });
    });

    tourHomeForm.addEventListener('submit', () => {
        quillEditors.forEach(({ textarea, quill }) => {
            const isEmpty = quill.getText().trim() === '';
            textarea.value = isEmpty ? '' : quill.root.innerHTML;
        });
    });
}

function applyUploadedTourImage(row, path) {
    const imagePathInput = row.querySelector('.performer-image-path');
    const downloadLink = row.querySelector('.performer-download-link');

    if (imagePathInput) imagePathInput.value = path;
    if (downloadLink) {
        downloadLink.href = path;
        downloadLink.classList.remove('d-none');
    }
}

function getUploadPath(payload) {
    return payload?.path
        ?? payload?.body?.path
        ?? payload?.url
        ?? payload?.body?.url
        ?? null;
}

async function uploadTourImage(row, button) {
    const fileInput = row.querySelector('.performer-upload-input');
    const sectionItemIdInput = row.querySelector('.tour-item-id');
    const currentPathInput = row.querySelector('.performer-image-path');

    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        alert('Please choose an image first.');
        return;
    }

    const formData = new FormData();
    formData.append('image', fileInput.files[0]);
    formData.append('module', 'tour');
    formData.append('current_path', currentPathInput ? currentPathInput.value : '');

    const sectionItemId = sectionItemIdInput ? Number(sectionItemIdInput.value || 0) : 0;
    if (sectionItemId > 0) {
        formData.append('section_item_id', String(sectionItemId));
    }

    button.disabled = true;
    try {
        const response = await fetch('/cms/media/upload-replace', {
            method: 'POST',
            body: formData
        });

        const payload = await response.json();
        const path = getUploadPath(payload);
        const success = payload?.success ?? response.ok;

        if (!response.ok || !success || !path) {
            throw new Error(payload?.message || payload?.body?.message || 'Upload failed');
        }

        applyUploadedTourImage(row, path);
    } catch (error) {
        alert(error?.message || 'Image upload failed.');
    } finally {
        button.disabled = false;
        fileInput.value = '';
    }
}

function initializeImageUpload() {
    if (!tourHomeForm) return;

    tourHomeForm.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement) || !target.classList.contains('upload-performer-image')) {
            return;
        }

        const row = target.closest('.performer-image-row, tr, .mb-3');
        if (!row) return;

        uploadTourImage(row, target);
    });
}

initializeQuillEditors();
initializeImageUpload();