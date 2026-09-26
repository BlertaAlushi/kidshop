<?php

namespace App\Services;

use App\Interfaces\Services\LookupInterface;
use App\Models\Color;

class ColorsService extends LookupBaseService implements LookupInterface
{
    public function __construct(){
        $this->model = Color::class;
    }

    public function store($data){
        $this->model::create([
            'name' => $data['name'],
            'hex_code' => $data['hex_code'] ?? null,
        ]);
    }

    public function update($data,$item){
        $item->update([
            'name' => $data['name'],
            'hex_code' => $data['hex_code'] ?? null,
        ]);
    }
}
