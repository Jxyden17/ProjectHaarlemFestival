(function () {
    // Replace flagged textareas with Quill editors and sync HTML back on submit.
    function initializeQuillEditors(form) {
        if (!(form instanceof HTMLFormElement) || typeof Quill === 'undefined') {
            return [];
        }

        const quillEditors = [];
        const toolbarOptions = [
            [{ header: [2, 3, 4, false] }],
            ['bold', 'italic', 'underline'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['link', 'blockquote'],
            ['clean']
        ];

        const richTextAreas = form.querySelectorAll('textarea[data-quill="1"]');
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

        form.addEventListener('submit', () => {
            quillEditors.forEach(({ textarea, quill }) => {
                const isEmpty = quill.getText().trim() === '';
                textarea.value = isEmpty ? '' : quill.root.innerHTML;
            });
        });

        return quillEditors;
    }

    window.CmsPageEditor = {
        initializeQuillEditors,
    };
})();
