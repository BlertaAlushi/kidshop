<?php

namespace App\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray($request)
    {
        return $this->resource;
    }
}
