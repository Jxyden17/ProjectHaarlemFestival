const artistsContainer = document.getElementById('artists-container');

function applyUploadedArtistImage(row, path) {
    const imagePathInput = row.querySelector('.artist-image');
    const downloadLink = row.querySelector('.artist-download-link');

    if (imagePathInput) {
        imagePathInput.value = path;
    }

    if (downloadLink) {
        downloadLink.href = path;
        downloadLink.classList.remove('d-none');
    }
}

async function uploadArtistImage(row, button) {
    const fileInput = row.querySelector('.artist-upload-input');
    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        alert('Please choose an image first.');
        return;
    }

    const formData = new FormData();
    formData.append('image', fileInput.files[0]);
    formData.append('module', 'dance_artist');

    const currentPathInput = row.querySelector('.artist-image');
    formData.append('current_path', currentPathInput ? currentPathInput.value : '');
    const sectionItemIdInput = row.querySelector('.artist-item-id');
    formData.append('section_item_id', sectionItemIdInput ? sectionItemIdInput.value : '');

    button.disabled = true;
    try {
        const response = await fetch('/cms/media/upload-replace', {
            method: 'POST',
            body: formData
        });

        const raw = await response.text();
        let parsed = null;
        try {
            parsed = JSON.parse(raw);
        } catch (e) {
            parsed = null;
        }

        if (!response.ok) {
            const message = parsed && parsed.message
                ? parsed.message
                : `Upload request failed (${response.status})`;
            throw new Error(message);
        }

        if (!parsed || !parsed.success || !parsed.path) {
            throw new Error((parsed && parsed.message) ? parsed.message : 'Upload failed');
        }

        applyUploadedArtistImage(row, parsed.path);
    } catch (error) {
        alert(error && error.message ? error.message : 'Image upload failed.');
    } finally {
        button.disabled = false;
        fileInput.value = '';
    }
}

if (artistsContainer) {
    artistsContainer.addEventListener('click', (event) => {
        if (event.target.classList.contains('upload-artist-image')) {
            const row = event.target.closest('.artist-row');
            if (!row) {
                return;
            }
            uploadArtistImage(row, event.target);
        }
    });
}
