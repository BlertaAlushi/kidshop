<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',

            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($productId),
            ],
            'description' => 'nullable|string',
            'gender' => 'required|in:boy,girl,unisex',
            'is_active' => 'nullable|boolean',

            'seasons' => 'nullable|array',
            'seasons.*' => 'exists:seasons,id',

            'variants' => 'required|array|min:1',
            'variants.*.id' => 'nullable|integer|exists:product_variants,id',
            'variants.*.size_id' => 'required|exists:sizes,id',
            'variants.*.color_id' => 'required|exists:colors,id',
            'variants.*.sku' => 'required|string|max:255',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock_quantity' => 'required|integer|min:0',
            'variants.*.is_active' => 'nullable|boolean',

            'existing_images' => 'nullable|array',
            'existing_images.*.id' => 'required|integer|exists:product_images,id',
            'existing_images.*.color_id' => 'nullable|exists:colors,id',
            'existing_images.*.is_primary' => 'nullable|boolean',
            'existing_images.*.sort_order' => 'nullable|integer|min:0',

            'new_images' => 'nullable|array',
            'new_images.*.file' => 'required|file|image|mimes:jpeg,png,jpg,webp|max:2048',
            'new_images.*.color_id' => 'nullable|exists:colors,id',
            'new_images.*.is_primary' => 'nullable|boolean',
            'new_images.*.sort_order' => 'nullable|integer|min:0',
        ];
    }
}
