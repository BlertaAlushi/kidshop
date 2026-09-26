<?php

namespace App\Services;

use App\Interfaces\Services\LookupInterface;
use App\Models\Size;

class SizesService extends LookupBaseService implements LookupInterface
{
    public function __construct(){
        $this->model = Size::class;
    }

    public function store($data){
        $this->model::create([
            'name' => $data['name'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    public function update($data,$item){
        $item->update([
            'name' => $data['name'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }
}
