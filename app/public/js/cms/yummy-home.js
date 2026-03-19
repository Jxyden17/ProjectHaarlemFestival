const yummyHomeForm = document.querySelector('form[action="/cms/events/yummy-home"]');
const editors = [];

function initYummyEditors() {

    if (!yummyHomeForm || typeof Quill === "undefined") {
        return;
    }

    const toolbar = [
        [{ header: [2, 3, 4, false] }],
        ["bold", "italic", "underline"],
        [{ list: "ordered" }, { list: "bullet" }],
        ["link"],
        ["clean"]
    ];

    const fields = yummyHomeForm.querySelectorAll('textarea[data-editor="rich"]');

    fields.forEach(field => {

        const wrapper = document.createElement("div");
        wrapper.classList.add("mb-2");

        field.insertAdjacentElement("afterend", wrapper);

        const editor = new Quill(wrapper, {
            theme: "snow",
            modules: { toolbar }
        });

        editor.root.innerHTML = field.value.trim() || "<p><br></p>";

        field.classList.add("d-none");
        field.required = false;

        editors.push({ field, editor });
    });

    yummyHomeForm.addEventListener("submit", (e) => {
        e.preventDefault();

        editors.forEach(entry => {
            const empty = entry.editor.getText().trim() === "";
            entry.field.value = empty ? "" : entry.editor.root.innerHTML;
        });

        yummyHomeForm.submit();
    });
}

initYummyEditors();