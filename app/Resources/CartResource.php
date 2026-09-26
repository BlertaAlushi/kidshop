<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray($request){
        $variant = $this->productVariant;
        $product = $variant->product;
        $image = $variant->color
            ? $product->images->firstWhere('color_id', $variant->color->id)
            : null;
        $image ??= $product->images->firstWhere('is_primary', true) ?? $product->images->first();

        return [
            'id' => $this->id,
            'product_variant_id' => $this->product_variant_id,
            'product_slug' => $product->slug,
            'name' => $product->name,
            'size' => $variant->size?->name,
            'color' => $variant->color?->name,
            'color_hex' => $variant->color?->hex_code,
            'price' => (float) $variant->price,
            'quantity' => $this->quantity,
            'image' => $image ? '/storage/'.$image->path : null,
        ];
    }
}
