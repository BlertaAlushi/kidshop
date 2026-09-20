<?php

namespace App\Services;

use App\Interfaces\Services\LookupInterface;
use App\Models\ProductType;

class ProductTypesService extends LookupBaseService implements LookupInterface
{
    public function __construct(){
        $this->model = ProductType::class;
    }
}
