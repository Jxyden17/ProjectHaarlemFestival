<?php
namespace App\Service\Interfaces;

use App\Models\Page\Page;
interface IPageService
{
    function buildPage(int $pageId): ?Page;
}