<?php

namespace App\Controllers;

use App\Models\Enums\UserRole;

class BaseController
{
    protected function render(string $view, array $data = []): void
    {
        $this->renderWithLayout($view, $data, __DIR__ . '/../Views/shared/layout.php');
    }

    protected function renderCms(string $view, array $data = []): void
    {
        $this->renderWithLayout($view, $data, __DIR__ . '/../Views/shared/cmsLayout.php');
    }

    private function renderWithLayout(string $view, array $data, string $layoutPath): void
    {
        extract($data);

        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        if (!is_file($viewPath)) {
            throw new \RuntimeException('View not found: ' . $view);
        }

        if (!is_file($layoutPath)) {
            throw new \RuntimeException('Layout not found: ' . $layoutPath);
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        require $layoutPath;
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
