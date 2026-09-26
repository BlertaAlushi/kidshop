<?php

namespace App\Http\Controllers\Collections;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\ProductsCollectionInterface;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Season;
use App\Resources\Products\ProductResource;
use App\Services\FilterOptionsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductsController extends Controller
{
    public function __construct(
        protected  ProductsCollectionInterface $productsCollection,
    ){}

    public function all(Request $request) {
        $filters = FilterOptionsService::filters($request);
        $products = $this->productsCollection->products($filters);
        return Inertia::render('Collections', [
            'filters' => $filters,
            'products' => $products,
        ]);
    }

    public function filterByCategory(Request $request, Category $category){
        $filters = FilterOptionsService::filters($request);
        $filters['categories'] = [$category->id];
        $products = $this->productsCollection->products($filters);
        return Inertia::render('Collections', [
            'filters' => $filters,
            'products' => $products,
        ]);
    }

    public function filterByMark(Request $request, Brand $mark){
        $filters = FilterOptionsService::filters($request);
        $filters['brands'] = [$mark->id];
        $products = $this->productsCollection->products($filters);
        return Inertia::render('Collections', [
            'filters' => $filters,
            'products' => $products,
        ]);
    }

    public function filterBySeason(Request $request, Season $season){
        $filters = FilterOptionsService::filters($request);
        $filters['seasons'] = [$season->id];
        $products = $this->productsCollection->products($filters);
        return Inertia::render('Collections', [
            'filters' => $filters,
            'products' => $products,
        ]);
    }

    public function filterByGender(Request $request, string $gender){
        $filters = FilterOptionsService::filters($request);
        $filters['gender'] = $gender;
        $products = $this->productsCollection->products($filters);
        return Inertia::render('Collections', [
            'filters' => $filters,
            'products' => $products,
        ]);
    }

    public function product(Product $product){
        $product->load(['category', 'brand', 'images', 'variants.size', 'variants.color']);
        return Inertia::render('Product', ['product' => new ProductResource($product)]);
    }
}
