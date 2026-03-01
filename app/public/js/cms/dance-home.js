const artistsContainer = document.getElementById('artists-container');
const passesContainer = document.getElementById('passes-container');
const artistTemplate = document.getElementById('artist-template');
const passTemplate = document.getElementById('pass-template');
const addArtistButton = document.getElementById('add-artist');
const addPassButton = document.getElementById('add-pass');

function reindexArtists() {
    const rows = artistsContainer.querySelectorAll('.artist-row');
    rows.forEach((row, idx) => {
        row.querySelector('.artist-name').name = `artists[${idx}][name]`;
        row.querySelector('.artist-genre').name = `artists[${idx}][genre]`;
        row.querySelector('.artist-image').name = `artists[${idx}][image]`;
    });
}

function reindexPasses() {
    const rows = passesContainer.querySelectorAll('.pass-row');
    rows.forEach((row, idx) => {
        row.querySelector('.pass-label').name = `passes[${idx}][label]`;
        row.querySelector('.pass-price').name = `passes[${idx}][price]`;
        row.querySelector('.pass-highlight').name = `passes[${idx}][highlight]`;
    });
}

function getArtistRowIndex(row) {
    const nameInput = row.querySelector('.artist-name');
    if (!nameInput || !nameInput.name) {
        return 0;
    }

    const match = nameInput.name.match(/artists\[(\d+)\]/);
    if (!match || !match[1]) {
        return 0;
    }

    return parseInt(match[1], 10);
}

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
    formData.append('slot_index', String(getArtistRowIndex(row)));
    formData.append('module', 'dance_artist');

    const currentPathInput = row.querySelector('.artist-image');
    formData.append('current_path', currentPathInput ? currentPathInput.value : '');

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

if (artistsContainer && passesContainer && artistTemplate && passTemplate && addArtistButton && addPassButton) {
    addArtistButton.addEventListener('click', () => {
        const node = artistTemplate.content.cloneNode(true);
        artistsContainer.appendChild(node);
        reindexArtists();
    });

    addPassButton.addEventListener('click', () => {
        const node = passTemplate.content.cloneNode(true);
        passesContainer.appendChild(node);
        reindexPasses();
    });

    artistsContainer.addEventListener('click', (event) => {
        if (event.target.classList.contains('upload-artist-image')) {
            const row = event.target.closest('.artist-row');
            if (!row) {
                return;
            }
            uploadArtistImage(row, event.target);
            return;
        }

        if (!event.target.classList.contains('remove-artist')) {
            return;
        }
        event.target.closest('.artist-row').remove();
        reindexArtists();
    });

    passesContainer.addEventListener('click', (event) => {
        if (!event.target.classList.contains('remove-pass')) {
            return;
        }
        event.target.closest('.pass-row').remove();
        reindexPasses();
    });

    reindexArtists();
    reindexPasses();
}
