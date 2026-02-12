<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Service Unavailable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <?php
    $errorTitle = $errorTitle ?? 'Service temporarily unavailable';
    $errorMessage = $errorMessage ?? 'Please try again in a moment.';
    ?>
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card bg-secondary-subtle border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h1 class="h4 mb-3 text-dark"><?= htmlspecialchars($errorTitle) ?></h1>
                        <p class="text-dark mb-0"><?= htmlspecialchars($errorMessage) ?></p>

                        <?php if (!empty($showDebug) && !empty($debugError)): ?>
                            <hr>
                            <h2 class="h6 text-dark mb-2">Debug</h2>
                            <pre class="bg-light border rounded p-2 text-danger small mb-0"><?= htmlspecialchars($debugError) ?></pre>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
