<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray($request){
        $data = [
            'id'=>$this->id,
            'product_id'=>$this->product_id,
            'user_id'=>$this->user_id,
            'name'=>$this->product->translation->name,
            'price'=>$this->product->price,
            'quantity'=>$this->quantity,
            'currency'=>$this->product->currency,
            'image'=>$this->product->image,
        ];
        return $data;
    }
}
