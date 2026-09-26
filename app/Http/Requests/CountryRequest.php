<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CountryRequest extends FormRequest
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
        $iso2 = $this->route('country')?->iso_2;

        return [
            'iso_2' => [
                'required',
                'string',
                'size:2',
                Rule::unique('countries', 'iso_2')->ignore($iso2, 'iso_2'),
            ],
            'country' => 'required|string|max:255',
            'delivery_fee' => 'required|numeric|min:0',
        ];
    }
}
