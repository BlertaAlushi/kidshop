<?php

namespace App\Services\Products;

use App\Interfaces\Services\ProductsCollectionInterface;
use App\Models\Product;
use App\Resources\Products\ProductResource;

class ProductsCollectionService implements ProductsCollectionInterface
{
    protected array $pivotFilters = [
        'skin_types'     => 'skinTypes',
        'skin_concerns'  => 'skinConcerns',
        'product_types'  => 'productTypes',
        'extras'         => 'extras',
        'body_parts'     => 'bodyParts',
        'marks'          => 'marks',
    ];
    public function products($filters){
        $query = Product::query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->whereHas('translation', function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        foreach ($this->pivotFilters as $filterKey => $relation) {
            if (!empty($filters[$filterKey])) {
                if($filterKey == 'marks'){
                    $query->where('mark_id', $filters[$filterKey]);
                }else {
                    $query->whereHas($relation, function ($q) use ($filters, $filterKey) {
                        $q->whereIn('id', $filters[$filterKey]);
                    });
                }
            }
        }


        switch ($filters['order_by']) {
            case 'price_high_to_low':
                $query->orderBy('price', 'desc');
                break;
            case 'price_low_to_high':
                $query->orderBy('price', 'asc');
                break;
            case 'date_old_to_new':
                $query->orderBy('created_at', 'asc');
                break;
            case 'availability':
                $query->orderBy('stock_quantity', 'desc');
                break;
                default:
            $query->orderBy('created_at', 'desc');
            break;

        }

        $products = $query
            ->with([
                'translation:product_id,language_id,name,description',
                'mark'
            ])
            ->select('id', 'slug', 'price', 'currency', 'image', 'mark_id','stock_quantity');

        $products = match ($filters['per_page']) {
            '24' => $products->paginate(24)->withQueryString(),
            '48' => $products->paginate(48)->withQueryString(),
            default => $products->paginate(12)->withQueryString(),
        };
        return ProductResource::collection($products);
    }

    public function newArrivals()
    {
        return ProductResource::collection(
            Product::with([
                'translation:product_id,language_id,name,description',
                'mark'
            ])
                ->latest()
                ->take(9)
                ->get()
        );
    }
}
