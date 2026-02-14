<?php

namespace App\Controllers;

class HistoryController extends BaseController
{
    public function index(): void
    {
        $email = $_SESSION['email'] ?? null;

        $this->render('history/index', [
            'title' => 'Home History'
        ]);
    }
}
