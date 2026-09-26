<?php

namespace App\Services;

use App\Interfaces\Services\LookupInterface;
use App\Models\Brand;
use Illuminate\Support\Str;

class MarksService extends LookupBaseService implements LookupInterface
{
    public function __construct(){
        $this->model = Brand::class;
    }

    public function store($data){
        $this->model::create([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? Str::slug($data['name']),
            'is_active' => $data['is_active'] ?? true,
        ]);
    }
    public function update($data,$item){
        $item->update([
            'name' => $data['name'],
            'slug' =>$data['slug'] ?? Str::slug($data['name']),
            'is_active' => $data['is_active'] ?? true,
        ]);
    }
}
