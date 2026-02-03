<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    public function index(): void
    {
        $username = $_SESSION['username'] ?? null;

        $this->render('home/index', [
            'title' => 'Home',
            'username' => $username
        ]);
    }
}
