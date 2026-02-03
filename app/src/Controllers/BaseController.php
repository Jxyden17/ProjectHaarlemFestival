<?php

namespace App\Controllers;

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
}