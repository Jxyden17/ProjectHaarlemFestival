const form = document.querySelector('form[action*="yummy-details"]');

if (form && window.CmsPageEditor) {
    window.CmsPageEditor.initializeQuillEditors(form);
}