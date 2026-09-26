<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Interfaces\Services\LookupInterface;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Season;
use App\Models\Size;
use Inertia\Inertia;

class ProductsController extends Controller
{
    public function __construct(
        protected LookupInterface $lookup
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = $this->lookup->index();
        return Inertia::render('admin/products/ProductIndex', ['products' => $products]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/products/ProductNew', $this->formOptions());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $this->lookup->store($data);
        return redirect()->route('admin.products.index')->with('success','created_success');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->load([
            'category',
            'brand',
            'seasons',
            'variants.size',
            'variants.color',
            'images.color',
        ]);

        return Inertia::render('admin/products/ProductEdit', array_merge(
            ['product' => $product],
            $this->formOptions()
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $this->lookup->update($data, $product);
        return redirect()->route('admin.products.index')->with('success','edited_success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->back()->with('success','deleted_success');
    }

    protected function formOptions(): array
    {
        return [
            'categories' => Category::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'brands' => Brand::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'sizes' => Size::orderBy('sort_order')->get(['id', 'name']),
            'colors' => Color::orderBy('name')->get(['id', 'name', 'hex_code']),
            'seasons' => Season::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ];
    }
}
