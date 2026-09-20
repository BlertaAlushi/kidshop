<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'slug' => [
                'nullable',
            ],

            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'stock_quantity' => 'required|integer',
            'price' => 'required|numeric',
            'currency' => 'required|string|max:10',
            'mark_id' => 'required|exists:marks,id',

            'image' => 'nullable|file|image|mimes:jpeg,png,jpg|max:2048',

            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.name' => 'required|string',
            'translations.*.description' => 'required|string',

            'body_parts' => 'nullable|array',
            'body_parts.*' => 'exists:body_parts,id',

            'skin_types' => 'nullable|array',
            'skin_types.*' => 'exists:skin_types,id',

            'skin_concerns' => 'nullable|array',
            'skin_concerns.*' => 'exists:skin_concerns,id',

            'product_types' => 'nullable|array',
            'product_types.*' => 'exists:product_types,id',

            'extras' => 'nullable|array',
            'extras.*' => 'exists:extras,id',
        ];
    }
}
