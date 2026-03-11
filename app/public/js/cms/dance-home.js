const danceHomeForm = document.querySelector('form[action="/cms/events/dance-home"]');
if (danceHomeForm && window.CmsPageEditor) {
    window.CmsPageEditor.initializeQuillEditors(danceHomeForm);
}
