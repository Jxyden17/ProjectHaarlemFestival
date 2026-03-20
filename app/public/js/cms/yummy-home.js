const form = document.querySelector('form[action*="yummy-home"]');

if (form && window.CmsPageEditor) {
    window.CmsPageEditor.initializeQuillEditors(form);
}