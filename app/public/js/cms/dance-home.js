const danceHomeForm = document.querySelector('form[action="/cms/events/dance-home"]');
const quillEditors = [];

function initializeQuillEditors() {
    if (!danceHomeForm || typeof Quill === 'undefined') {
        return;
    }

    const toolbarOptions = [
        [{ header: [1, 2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ script: 'sub' }, { script: 'super' }],
        [{ indent: '-1' }, { indent: '+1' }],
        [{ direction: 'rtl' }],
        [{ size: ['small', false, 'large', 'huge'] }],
        [{ color: [] }, { background: [] }],
        [{ font: [] }],
        [{ align: [] }],
        ['link', 'blockquote', 'code-block'],
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
        quill.root.innerHTML = initialHtml === '' ? '<p><br></p>' : initialHtml;
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
