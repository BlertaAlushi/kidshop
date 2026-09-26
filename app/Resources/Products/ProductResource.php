<?php

namespace App\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        $variants = $this->variants;
        $defaultVariant = $variants?->firstWhere('is_active', true) ?? $variants?->first();

        $images = $this->images;
        $image = $images?->firstWhere('is_primary', true) ?? $images?->first();

        $colors = $variants
            ?->pluck('color')
            ->filter()
            ->unique('id')
            ->values()
            ->map(function ($color) use ($images) {
                $colorImages = $images
                    ?->where('color_id', $color->id)
                    ->sortBy('sort_order')
                    ->values()
                    ->map(fn ($image) => '/storage/'.$image->path) ?? collect();

                return [
                    'id' => $color->id,
                    'name' => $color->name,
                    'hex_code' => $color->hex_code,
                    'image' => $colorImages->first(),
                    'images' => $colorImages->values()->all(),
                ];
            })
            ->values();

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'gender' => $this->gender,
            'category' => $this->whenLoaded('category', fn () => $this->category?->name),
            'brand' => $this->whenLoaded('brand', fn () => $this->brand?->name),
            'image' => $image ? '/storage/'.$image->path : null,
            'price' => $defaultVariant?->price !== null ? (float) $defaultVariant->price : null,
            'stock_quantity' => (int) ($variants?->sum('stock_quantity') ?? 0),
            'default_variant' => $defaultVariant ? [
                'id' => $defaultVariant->id,
                'price' => (float) $defaultVariant->price,
                'stock_quantity' => $defaultVariant->stock_quantity,
            ] : null,
            'colors' => $colors ?? [],
            'variants' => $variants?->map(fn ($variant) => [
                'id' => $variant->id,
                'price' => (float) $variant->price,
                'stock_quantity' => $variant->stock_quantity,
                'is_active' => (bool) $variant->is_active,
                'size' => $variant->relationLoaded('size') && $variant->size ? [
                    'id' => $variant->size->id,
                    'name' => $variant->size->name,
                    'sort_order' => $variant->size->sort_order,
                ] : null,
                'color' => $variant->relationLoaded('color') && $variant->color ? [
                    'id' => $variant->color->id,
                    'name' => $variant->color->name,
                    'hex_code' => $variant->color->hex_code,
                ] : null,
            ])->values() ?? [],
        ];
    }
}
