<?php

namespace App\Service\Cms\Interfaces;


use App\Models\Page\Page;

interface ICmsJazzService
{
    public function getJazzHomePage(): Page;
    
}
