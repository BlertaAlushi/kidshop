<?php

namespace App\Services\Products;

use App\Interfaces\Services\LookupInterface;
use App\Models\Product;
use App\Resources\Products\ProductResource;
use App\Services\LookupBaseService;
use Illuminate\Support\Str;

class ProductsService extends LookupBaseService implements LookupInterface
{
    public function __construct(){
        $this->model = Product::class;
    }

    public function index(){
        $products = $this->model::with('mark:id,name')->get();
        return ProductResource::collection($products);
    }

    public function store($data){
        $create = [
            'name' => $data['name'],
            'slug' => $data['slug'] ?? Str::slug($data['name']),
            'description' => $data['description'],
            'stock_quantity' => $data['stock_quantity'],
            'price' => $data['price'],
            'currency' => $data['currency'],
            'mark_id' => $data['mark_id'],
        ];
        if(!empty($data['image'])){
            $create['image'] = $data['image'];
        }
        $product = $this->model::create($create);

        foreach ($data['translations'] as $translation) {
            $product->translations()->create([
                'language_id' => $translation['language_id'],
                'name' => $translation['name'],
                'description' => $translation['description'],
            ]);
        }

        $this->syncPivots($product, $data);
    }

    public function update($data,$item){
        $item->update([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? Str::slug($data['name']),
            'description' => $data['description'],
            'stock_quantity' => $data['stock_quantity'],
            'price' => $data['price'],
            'currency' => $data['currency'],
            'mark_id' => $data['mark_id'],
            'image' => $data['image'],
        ]);

        foreach ($data['translations'] as $translation) {
            $updated = $item->translations()
                ->where('language_id', $translation['language_id'])
                ->update([
                    'name' => $translation['name'],
                    'description' => $translation['description'],
                ]);

            if (!$updated) {
                $item->translations()->create([
                    'language_id' => $translation['language_id'],
                    'name' => $translation['name'],
                    'description' => $translation['description'],
                ]);
            }
        }

        $this->syncPivots($item, $data);
    }

    /**
     * @param $product
     * @param $data
     * @return void
     */
    public function syncPivots($product, $data): void
    {
        $product->bodyParts()->sync($data['body_parts'] ?? []);
        $product->skinTypes()->sync($data['skin_types'] ?? []);
        $product->skinConcerns()->sync($data['skin_concerns'] ?? []);
        $product->productTypes()->sync($data['product_types'] ?? []);
        $product->extras()->sync($data['extras'] ?? []);
    }
}
