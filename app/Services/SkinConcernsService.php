<?php

namespace App\Services;

use App\Interfaces\Repositories\ProductRepositoryInterface;
use App\Interfaces\Services\LookupInterface;
use App\Models\SkinConcern;

class SkinConcernsService extends LookupBaseService implements LookupInterface
{
    public function __construct(){
        $this->model = SkinConcern::class;
    }
}
