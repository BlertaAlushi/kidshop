<?php

namespace App\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'slug' => $this->slug,
            'price' => $this->price,
            'currency' => $this->currency,
            'stock_quantity' => $this->stock_quantity?:0,
            'name' => $this->translation?$this->translation->name:$this->name,
            'description' =>$this->translation?$this->translation->description:$this->description,
            'mark'=>$this->mark?$this->mark->name:"",
            'image'=>$this->image
        ];
    }
}
