<?php

namespace App\Services\Products;

use App\Interfaces\Services\ProductsCollectionInterface;
use App\Models\Color;
use App\Models\Product;
use App\Resources\Products\ProductVariantResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request as RequestFacade;

class ProductsCollectionService implements ProductsCollectionInterface
{
    public function __construct(
        protected PromotionPricingService $promotionPricing,
    ) {}

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

        $query->orderBy('created_at', 'desc');

        $products = $query->with(['category', 'brand', 'images', 'variants.color'])->get();

        $items = $this->buildItemsByColor($products);

        if (!empty($filters['colors'])) {
            $items = $items->filter(fn ($item) => in_array($item['color_id'], $filters['colors']));
        }

        if (!empty($filters['on_sale'])) {
            $items = $items->filter(fn ($item) => $item['promotion'] !== null);
        }

        switch ($filters['order_by'] ?? null) {
            case 'price_high_to_low':
                $items = $items->sortByDesc('price');
                break;
            case 'price_low_to_high':
                $items = $items->sortBy('price');
                break;
            case 'date_old_to_new':
                $items = $items->sortBy('created_at');
                break;
            default:
                $items = $items->sortByDesc('created_at');
                break;
        }

        $items = $items->values();

        $perPage = match ($filters['per_page'] ?? null) {
            '24' => 24,
            '48' => 48,
            default => 12,
        };

        $page = (int) RequestFacade::input('page', 1);

        $paginated = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => RequestFacade::url(),
                'query' => RequestFacade::query(),
            ]
        );

        return ProductVariantResource::collection($paginated);
    }

    private function buildItemsByColor($products)
    {
        return $products->flatMap(function (Product $product) {
            $variantsByColor = $product->variants->groupBy('color_id');

            $productColors = $product->variants
                ->pluck('color')
                ->filter()
                ->unique('id')
                ->values();

            if ($variantsByColor->isEmpty()) {
                return [$this->buildItem($product, null, $product->variants, $productColors)];
            }

            return $variantsByColor->map(
                fn ($variants) => $this->buildItem($product, $variants->first()->color, $variants, $productColors)
            )->values()->all();
        });
    }

    private function buildItem(Product $product, ?Color $color, $variants, $productColors): array
    {
        $activeVariants = $variants->where('is_active', true);
        $defaultVariant = $activeVariants->first() ?? $variants->first();

        $colorImages = $color
            ? $product->images->where('color_id', $color->id)->sortBy('sort_order')->values()
            : collect();

        $fallbackImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
        $image = $colorImages->first() ?? $fallbackImage;

        $pricing = $defaultVariant
            ? $this->promotionPricing->priceFor($product, $color?->id, (float) $defaultVariant->price)
            : ['price' => null, 'original_price' => null, 'promotion' => null];

        return [
            'id' => $product->id.'-'.($color?->id ?? 'none'),
            'product_id' => $product->id,
            'slug' => $product->slug,
            'name' => $product->name,
            'gender' => $product->gender,
            'category' => $product->category?->name,
            'brand' => $product->brand?->name,
            'image' => $image ? '/storage/'.$image->path : null,
            'images' => $colorImages->map(fn ($img) => '/storage/'.$img->path)->values()->all(),
            'price' => $pricing['price'],
            'original_price' => $pricing['original_price'],
            'promotion' => $pricing['promotion'],
            'stock_quantity' => (int) $variants->sum('stock_quantity'),
            'color' => $color ? [
                'id' => $color->id,
                'name' => $color->name,
                'hex_code' => $color->hex_code,
            ] : null,
            'color_id' => $color?->id,
            'colors' => $productColors->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'hex_code' => $c->hex_code,
            ])->values()->all(),
            'created_at' => $product->created_at,
        ];
    }

    public function newArrivals()
    {
        $products = Product::where('is_active', true)
            ->with(['category', 'brand', 'images', 'variants.color'])
            ->latest()
            ->take(9)
            ->get();

        $items = $this->buildItemsByColor($products);

        return ProductVariantResource::collection($items);
    }
}
