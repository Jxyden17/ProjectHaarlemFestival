const danceHomeForm = document.querySelector('form[action="/cms/events/dance-home"]');

// Upgrade the dance home rich text fields when the editor is present.
if (danceHomeForm && window.CmsPageEditor) {
    window.CmsPageEditor.initializeQuillEditors(danceHomeForm);
}

// Submit the dance home editor through the shared save API flow.
if (danceHomeForm && window.CmsFormSaveAPI) {
    window.CmsFormSaveAPI.initialize(danceHomeForm);
}
