<?php

namespace App\Interfaces\Repositories;

interface ProductRepositoryInterface
{
    public function queryMenuOption($option);
    public function queryFilerOptions($filterOptions);
}
