<?php

namespace App\Http\Controllers\Collections;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\ProductsCollectionInterface;
use App\Models\BodyPart;
use App\Models\Extra;
use App\Models\Mark;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\SkinConcern;
use App\Models\SkinType;
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

    public function filterByBodyPart(Request $request, BodyPart $bodyPart){
        $filters = FilterOptionsService::filters($request);
        $filters["body_parts"]= [$bodyPart->id];
        $products = $this->productsCollection->products($filters);
        unset($filters["body_parts"]);
        return Inertia::render('Collections', [
            'filters' => $filters,
            'products' => $products,
        ]);
    }

    public function filterBySkinType(Request $request, SkinType $skinType){
        $filters = FilterOptionsService::filters($request);
        $filters["skin_types"][] = $skinType->id;
        $products = $this->productsCollection->products($filters);
        return Inertia::render('Collections', [
            'filters' => $filters,
            'products' => $products,
        ]);
    }

    public function filterBySkinConcern(Request $request, SkinConcern $skinConcern){
        $filters = FilterOptionsService::filters($request);
        $filters["skin_concerns"][] = $skinConcern->id;
        $products = $this->productsCollection->products($filters);
        return Inertia::render('Collections', [
            'filters' => $filters,
            'products' => $products,
        ]);
    }

    public function filterByProductType(Request $request, ProductType $productType){
        $filters = FilterOptionsService::filters($request);
        $filters["product_types"][] = $productType->id;
        $products = $this->productsCollection->products($filters);
        return Inertia::render('Collections', [
            'filters' => $filters,
            'products' => $products,
        ]);
    }

    public function filterByExtra(Request $request, Extra $extra){
        $filters = FilterOptionsService::filters($request);
        $filters["extras"][] = $extra->id;
        $products = $this->productsCollection->products($filters);
        return Inertia::render('Collections', [
            'filters' => $filters,
            'products' => $products,
        ]);
    }

    public function filterByMark(Request $request, Mark $mark){
        $filters = FilterOptionsService::filters($request);
        $filters["marks"] = [$mark->id];
        $products = $this->productsCollection->products($filters);
        unset($filters["marks"]);
        return Inertia::render('Collections', [
            'filters' => $filters,
            'products' => $products,
        ]);
    }

    public function product(Product $product){
        $product->load([
            'translation:product_id,language_id,name,description',
            'mark'
        ]);
        return Inertia::render('Product', ['product' => new ProductResource($product)]);
    }
}
