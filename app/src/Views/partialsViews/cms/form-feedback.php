<?php
$success = !empty($success);
$errorMessage = isset($error) ? trim((string)$error) : '';
$successMessage = isset($successMessage) ? trim((string)$successMessage) : 'Saved successfully.';
?>

<?php if ($success): ?>
    <div class="alert alert-success" role="alert">
        <?= htmlspecialchars($successMessage) ?>
    </div>
<?php endif; ?>

<?php if ($errorMessage !== ''): ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($errorMessage) ?>
    </div>
<?php endif; ?>
