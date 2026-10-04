<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PromotionRequest;
use App\Interfaces\Services\LookupInterface;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Promotion;
use Inertia\Inertia;

class PromotionsController extends Controller
{
    public function __construct(
        protected LookupInterface $lookup,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promotions = $this->lookup->index();
        return Inertia::render('admin/promotions/PromotionIndex', ['promotions' => $promotions]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/promotions/PromotionNew', $this->formOptions());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PromotionRequest $request)
    {
        $data = $request->validated();
        $this->lookup->store($data);
        return redirect()->route('admin.promotions.index')->with('success', 'created_success');
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
    public function edit(Promotion $promotion)
    {
        $promotion->load('targets');

        return Inertia::render('admin/promotions/PromotionEdit', array_merge(
            ['promotion' => $this->transformForEdit($promotion)],
            $this->formOptions()
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PromotionRequest $request, Promotion $promotion)
    {
        $data = $request->validated();
        $this->lookup->update($data, $promotion);
        return redirect()->route('admin.promotions.index')->with('success', 'edited_success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promotion)
    {
        $promotion->delete();
        return redirect()->back()->with('success', 'deleted_success');
    }

    protected function formOptions(): array
    {
        return [
            'products' => $this->productOptions(),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'brands' => Brand::orderBy('name')->get(['id', 'name']),
            'colors' => Color::orderBy('name')->get(['id', 'name', 'hex_code']),
        ];
    }

    /**
     * Products with what the picker needs to search, filter and pick colors.
     */
    protected function productOptions(): array
    {
        return Product::query()
            ->with([
                'images' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('sort_order'),
                'variants:id,product_id,color_id',
            ])
            ->orderBy('name')
            ->get(['id', 'name', 'category_id', 'brand_id', 'is_active'])
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'category_id' => $product->category_id,
                'brand_id' => $product->brand_id,
                'is_active' => (bool) $product->is_active,
                'image' => $product->images->first()?->path
                    ? '/storage/'.$product->images->first()->path
                    : null,
                'color_ids' => $product->variants->pluck('color_id')->filter()->unique()->values(),
            ])
            ->all();
    }

    protected function transformForEdit(Promotion $promotion): array
    {
        return [
            'id' => $promotion->id,
            'name' => $promotion->name,
            'type' => $promotion->type,
            'value' => $promotion->value,
            'starts_at' => $promotion->starts_at,
            'ends_at' => $promotion->ends_at,
            'is_active' => $promotion->is_active,
            'targets' => $promotion->targets->map(function ($target) {
                return [
                    'id' => $target->id,
                    'type' => $target->product_id ? 'product' : ($target->category_id ? 'category' : 'brand'),
                    'target_id' => $target->product_id ?? $target->category_id ?? $target->brand_id,
                    'color_id' => $target->color_id,
                ];
            })->values(),
        ];
    }
}
