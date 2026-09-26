<?php

namespace App\Services\Products;

use App\Interfaces\Services\LookupInterface;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Services\LookupBaseService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductsService extends LookupBaseService implements LookupInterface
{
    public function __construct()
    {
        $this->model = Product::class;
    }

    public function index()
    {
        return $this->model::query()
            ->with(['category:id,name', 'brand:id,name'])
            ->withCount('variants')
            ->get(['id', 'category_id', 'brand_id', 'name', 'slug', 'gender', 'is_active']);
    }

    public function store($data)
    {
        $product = $this->model::create([
            'category_id' => $data['category_id'],
            'brand_id' => $data['brand_id'],
            'name' => $data['name'],
            'slug' => $data['slug'] ?? Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'gender' => $data['gender'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        $this->syncSeasons($product, $data);
        $this->syncVariants($product, $data);
        $this->syncImages($product, $data);

        return $product;
    }

    public function update($data, $item)
    {
        $item->update([
            'category_id' => $data['category_id'],
            'brand_id' => $data['brand_id'],
            'name' => $data['name'],
            'slug' => $data['slug'] ?? Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'gender' => $data['gender'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        $this->syncSeasons($item, $data);
        $this->syncVariants($item, $data);
        $this->syncImages($item, $data);

        return $item;
    }

    protected function syncSeasons(Product $product, array $data): void
    {
        $product->seasons()->sync($data['seasons'] ?? []);
    }

    protected function syncVariants(Product $product, array $data): void
    {
        $keepIds = [];

        foreach ($data['variants'] ?? [] as $variant) {
            $attributes = [
                'size_id' => $variant['size_id'],
                'color_id' => $variant['color_id'],
                'sku' => $variant['sku'],
                'price' => $variant['price'],
                'stock_quantity' => $variant['stock_quantity'],
                'is_active' => $variant['is_active'] ?? true,
            ];

            /** @var ProductVariant|null $row */
            $row = !empty($variant['id'])
                ? $product->variants()->find($variant['id'])
                : null;

            if ($row) {
                $row->update($attributes);
            } else {
                $row = $product->variants()->create($attributes);
            }

            $keepIds[] = $row->id;
        }

        $product->variants()->whereNotIn('id', $keepIds)->delete();
    }

    protected function syncImages(Product $product, array $data): void
    {
        $keepIds = [];
        $primaryAssigned = false;

        foreach ($data['existing_images'] ?? [] as $image) {
            /** @var ProductImage|null $row */
            $row = $product->images()->find($image['id']);

            if (!$row) {
                continue;
            }

            $isPrimary = ($image['is_primary'] ?? false) && !$primaryAssigned;
            $primaryAssigned = $primaryAssigned || $isPrimary;

            $row->update([
                'color_id' => $image['color_id'] ?? null,
                'is_primary' => $isPrimary,
                'sort_order' => $image['sort_order'] ?? 0,
            ]);

            $keepIds[] = $row->id;
        }

        $removedImages = $product->images()->whereNotIn('id', $keepIds)->get();
        foreach ($removedImages as $removedImage) {
            Storage::disk('public')->delete($removedImage->path);
        }
        $product->images()->whereNotIn('id', $keepIds)->delete();

        foreach ($data['new_images'] ?? [] as $image) {
            if (empty($image['file'])) {
                continue;
            }

            $isPrimary = ($image['is_primary'] ?? false) && !$primaryAssigned;
            $primaryAssigned = $primaryAssigned || $isPrimary;

            $product->images()->create([
                'color_id' => $image['color_id'] ?? null,
                'path' => $image['file']->store('products', 'public'),
                'sort_order' => $image['sort_order'] ?? 0,
                'is_primary' => $isPrimary,
            ]);
        }
    }
}
