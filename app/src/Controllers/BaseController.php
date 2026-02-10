<?php

namespace App\Controllers;

use App\Models\Enums\UserRole;

class BaseController
{
    protected function render(string $view, array $data = []): void
    {
        extract($data);

        // Capture the view output
        ob_start();
        require __DIR__ . '/../Views/' . $view . '.php';
        $content = ob_get_clean();

        // Include the layout
        require __DIR__ . '/../Views/layout.php';
    }
    protected function requireAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireAuth();

        $roleId = (int)($_SESSION['role_id'] ?? 0);
        if ($roleId !== UserRole::Administrator->value) {
            http_response_code(403);
            header('Location: /');
            exit;
        }
    }
}
