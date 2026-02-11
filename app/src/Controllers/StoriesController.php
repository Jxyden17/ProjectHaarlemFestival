<?php

namespace App\Controllers;

class StoriesController extends BaseController
{
    public function index(): void
    {
        $email = $_SESSION['email'] ?? null;

        $this->render('stories/index', [
            'title' => 'Home Stories'
        ]);
    }
}
