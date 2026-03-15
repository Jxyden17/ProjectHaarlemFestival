<?php
$success = !empty($success);
$errorMessage = isset($error) ? trim((string)$error) : '';
$successMessage = isset($successMessage) ? trim((string)$successMessage) : 'Saved successfully.';
$feedbackClass = 'd-none';
$feedbackVariant = 'success';
$feedbackMessage = '';

if ($success) {
    $feedbackClass = '';
    $feedbackVariant = 'success';
    $feedbackMessage = $successMessage;
} elseif ($errorMessage !== '') {
    $feedbackClass = '';
    $feedbackVariant = 'danger';
    $feedbackMessage = $errorMessage;
}
?>

<div
    class="alert alert-<?= htmlspecialchars($feedbackVariant) ?> <?= htmlspecialchars($feedbackClass) ?>"
    role="alert"
    data-cms-form-feedback="1"
>
    <?= htmlspecialchars($feedbackMessage) ?>
</div>
