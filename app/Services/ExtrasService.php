<?php

namespace App\Services;

use App\Interfaces\Services\LookupInterface;
use App\Models\Extra;

class ExtrasService extends LookupBaseService implements LookupInterface
{
    public function __construct(){
        $this->model = Extra::class;
    }
}
