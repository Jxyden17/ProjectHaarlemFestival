const scheduleForm = document.querySelector('form[action*="/cms/events/"][action$="/schedule"]');

function applyUploadedPerformerImage(row, path) {
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

async function uploadPerformerImage(row, button) {
    const fileInput = row.querySelector('.performer-upload-input');
    const sectionItemIdInput = row.querySelector('.performer-artist-item-id');
    const currentPathInput = row.querySelector('.performer-artist-image');

    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        alert('Please choose an image first.');
        return;
    }

    const sectionItemId = sectionItemIdInput ? Number(sectionItemIdInput.value || 0) : 0;
    if (sectionItemId <= 0) {
        alert('Missing artist image row. Please configure artist images in dance content first.');
        return;
    }

    const formData = new FormData();
    formData.append('image', fileInput.files[0]);
    formData.append('module', 'dance_artist');
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

        applyUploadedPerformerImage(row, payload.path);
    } catch (error) {
        alert(error && error.message ? error.message : 'Image upload failed.');
    } finally {
        button.disabled = false;
        fileInput.value = '';
    }
}

if (scheduleForm) {
    scheduleForm.addEventListener('click', (event) => {
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
}
