<?php

namespace App\Services;

use App\Interfaces\Services\LookupInterface;
use App\Models\SkinType;

class SkinTypesService extends LookupBaseService implements LookupInterface
{
    public function __construct(){
        $this->model = SkinType::class;
    }
}
