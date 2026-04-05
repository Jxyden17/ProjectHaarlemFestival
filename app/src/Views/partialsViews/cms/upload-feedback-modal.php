<?php
$isDebugMode = filter_var($_ENV['APP_DEBUG'] ?? getenv('APP_DEBUG') ?? false, FILTER_VALIDATE_BOOLEAN);
?>
<div
    class="modal fade"
    id="cmsUploadFeedbackModal"
    tabindex="-1"
    aria-labelledby="cmsUploadFeedbackModalLabel"
    aria-hidden="true"
    data-debug-enabled="<?= $isDebugMode ? '1' : '0' ?>"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content cms-feedback-modal">
            <div class="modal-header cms-feedback-modal__header">
                <h2 class="modal-title fs-5" id="cmsUploadFeedbackModalLabel">Upload status</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body cms-feedback-modal__body">
                <div class="alert mb-0 cms-feedback-modal__alert" id="cmsUploadFeedbackAlert" role="alert">
                    <p class="mb-0" id="cmsUploadFeedbackMessage"></p>
                </div>
            </div>
            <div class="modal-footer cms-feedback-modal__footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
