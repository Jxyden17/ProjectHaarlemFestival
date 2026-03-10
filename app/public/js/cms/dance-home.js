const danceHomeForm = document.querySelector('form[action="/cms/events/dance-home"]');
const quillEditors = [];

function initializeQuillEditors() {
    if (!danceHomeForm || typeof Quill === 'undefined') {
        return;
    }

    const toolbarOptions = [
        [{ header: [2, 3, 4, false] }],
        ['bold', 'italic', 'underline'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['link', 'blockquote'],
        ['clean']
    ];

    const richTextAreas = danceHomeForm.querySelectorAll('textarea[data-quill="1"]');
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

    danceHomeForm.addEventListener('submit', () => {
        quillEditors.forEach(({ textarea, quill }) => {
            const isEmpty = quill.getText().trim() === '';
            textarea.value = isEmpty ? '' : quill.root.innerHTML;
        });
    });
}

initializeQuillEditors();
