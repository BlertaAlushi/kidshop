<?php

namespace App\Services\Products;

use App\Interfaces\Services\ProductsCollectionInterface;
use App\Models\Product;
use App\Resources\Products\ProductResource;

class ProductsCollectionService implements ProductsCollectionInterface
{
    public function products($filters)
    {
        $query = Product::query()->where('is_active', true);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        if (!empty($filters['categories'])) {
            $query->whereIn('category_id', $filters['categories']);
        }

        if (!empty($filters['brands'])) {
            $query->whereIn('brand_id', $filters['brands']);
        }

        if (!empty($filters['gender'])) {
            $query->whereIn('gender', [$filters['gender'], 'unisex']);
        }

        if (!empty($filters['seasons'])) {
            $query->whereHas('seasons', function ($q) use ($filters) {
                $q->whereIn('seasons.id', $filters['seasons']);
            });
        }

        if (!empty($filters['colors'])) {
            $query->whereHas('variants', function ($q) use ($filters) {
                $q->whereIn('color_id', $filters['colors']);
            });
        }

        if (!empty($filters['sizes'])) {
            $query->whereHas('variants', function ($q) use ($filters) {
                $q->whereIn('size_id', $filters['sizes']);
            });
        }

        $query->withMin('variants', 'price');

        switch ($filters['order_by'] ?? null) {
            case 'price_high_to_low':
                $query->orderByDesc('variants_min_price');
                break;
            case 'price_low_to_high':
                $query->orderBy('variants_min_price');
                break;
            case 'date_old_to_new':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->with(['category', 'brand', 'images', 'variants.color']);

        $products = match ($filters['per_page'] ?? null) {
            '24' => $products->paginate(24)->withQueryString(),
            '48' => $products->paginate(48)->withQueryString(),
            default => $products->paginate(12)->withQueryString(),
        };

        return ProductResource::collection($products);
    }

    public function newArrivals()
    {
        return ProductResource::collection(
            Product::where('is_active', true)
                ->with(['category', 'brand', 'images', 'variants.color'])
                ->latest()
                ->take(9)
                ->get()
        );
    }
}
