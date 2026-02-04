<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    public function index(): void
    {
        $email = $_SESSION['email'] ?? null;

        $this->render('home/index', [
            'title' => 'Home',
            'email' => $email
        ]);
    }
}
