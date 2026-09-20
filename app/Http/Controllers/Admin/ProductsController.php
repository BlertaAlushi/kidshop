<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Interfaces\Services\LookupInterface;
use App\Models\Product;
use Illuminate\Http\Request;
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
        return Inertia::render('admin/products/ProductNew');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

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
            'translations',
            'bodyParts',
            'productTypes',
            'skinTypes',
            'skinConcerns',
            'extras'
        ]);
        return Inertia::render('admin/products/ProductEdit', [
            'product' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

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

}
