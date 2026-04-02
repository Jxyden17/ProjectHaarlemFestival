<div class="container py-4">
    <h1 class="h3 mb-3">your profile</h1>
    <a href="/" class="btn btn-sm btn-outline-secondary">Back to home</a>
    <div class="mb-3">
        <ol class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto">
                    <div class="fw-bold">name</div>
                    <?= htmlspecialchars($user->name) ?>
                </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto">
                    <div class="fw-bold">phone number</div>
                    <?= htmlspecialchars($user->phoneNumber) ?>
                </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto">
                    <div class="fw-bold">country</div>
                    <?= htmlspecialchars($user->country) ?>
                </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto">
                    <div class="fw-bold">city</div>
                    <?= htmlspecialchars($user->city) ?>
                </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto">
                    <div class="fw-bold">address</div>
                   <?= htmlspecialchars($user->addres) ?>
                </div>
            </li>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto">
                    <div class="fw-bold">postcode</div>
                   <?= htmlspecialchars($user->postcode) ?>
                </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto">
                    <div class="fw-bold">Email</div>
                    <?= htmlspecialchars($user->email) ?>
                </div>
            </li>
        </ol>
    </div>
    <div class="d-flex gap-2">
        <a href="/cms/users/editSelf?id=<?= $user->id ?>" class="btn btn-primary">edit profile</a>
    </div>

</div>
