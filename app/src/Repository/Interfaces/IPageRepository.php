<?php
namespace App\Repository\Interfaces;


interface IPageRepository
{
    public function getPageDataByTitle($slug);
}
