<?php

namespace App\Services;

use App\Interfaces\Services\LookupInterface;
use App\Models\BodyPart;

class BodyPartsService extends LookupBaseService implements LookupInterface
{
    public function  __construct(){
        $this->model = BodyPart::class;
    }
}
