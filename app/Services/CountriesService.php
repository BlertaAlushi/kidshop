<?php

namespace App\Services;

use App\Interfaces\Services\LookupInterface;
use App\Models\Country;

class CountriesService extends LookupBaseService implements LookupInterface
{
    public function __construct(){
        $this->model = Country::class;
    }

    public function store($data){
        $this->model::create([
            'iso_2' => strtoupper($data['iso_2']),
            'country' => $data['country'],
            'delivery_fee' => $data['delivery_fee'] ?? 0,
        ]);
    }

    public function update($data,$item){
        $item->update([
            'country' => $data['country'],
            'delivery_fee' => $data['delivery_fee'] ?? 0,
        ]);
    }
}
