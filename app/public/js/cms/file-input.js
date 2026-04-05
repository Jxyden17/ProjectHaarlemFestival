(function () {
    const INPUT_SELECTOR = '.performer-upload-input';

    // Show the selected file name next to the custom picker button.
    function getFileLabelText(input) {
        if (!(input instanceof HTMLInputElement) || !input.files || input.files.length === 0) {
            return 'No file selected';
        }

        return input.files[0].name;
    }

    // Replace the native file input chrome with a Bootstrap-styled picker shell.
    function enhanceFileInput(input, index) {
        if (!(input instanceof HTMLInputElement) || input.dataset.cmsFileEnhanced === '1') {
            return;
        }

        const inputId = input.id && input.id.trim() !== '' ? input.id : `cms-file-input-${index}`;
        input.id = inputId;
        input.dataset.cmsFileEnhanced = '1';
        input.classList.add('cms-file-input-native');

        const picker = document.createElement('div');
        picker.className = 'cms-file-picker';

        const trigger = document.createElement('label');
        trigger.className = 'btn btn-sm btn-outline-secondary mb-0';
        trigger.htmlFor = inputId;
        trigger.textContent = 'Choose file';

        const fileName = document.createElement('span');
        fileName.className = 'cms-file-picker__label';
        fileName.textContent = getFileLabelText(input);

        picker.appendChild(trigger);
        picker.appendChild(fileName);

        input.insertAdjacentElement('afterend', picker);

        input.addEventListener('change', function () {
            fileName.textContent = getFileLabelText(input);
        });
    }

    // Enhance every matching CMS upload input on the page.
    function initFileInputs() {
        document.querySelectorAll(INPUT_SELECTOR).forEach(enhanceFileInput);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFileInputs);
        return;
    }

    initFileInputs();
})();
