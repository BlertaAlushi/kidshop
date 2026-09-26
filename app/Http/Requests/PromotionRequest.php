<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class PromotionRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    if ($this->input('type') === 'percentage' && $value > 100) {
                        $fail('The value must not be greater than 100 when type is percentage.');
                    }
                },
            ],
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'nullable|boolean',

            'targets' => 'required|array|min:1',
            'targets.*.type' => 'required|in:product,category,brand',
            'targets.*.target_id' => 'required|integer',
            'targets.*.color_id' => 'nullable|integer|exists:colors,id',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            foreach ($this->input('targets', []) as $index => $target) {
                $type = $target['type'] ?? null;
                $targetId = $target['target_id'] ?? null;
                $colorId = $target['color_id'] ?? null;

                $table = match ($type) {
                    'product' => 'products',
                    'category' => 'categories',
                    'brand' => 'brands',
                    default => null,
                };

                if ($table && $targetId && !DB::table($table)->where('id', $targetId)->exists()) {
                    $validator->errors()->add(
                        "targets.$index.target_id",
                        'The selected target does not exist.'
                    );
                }

                if ($colorId && $type !== 'product') {
                    $validator->errors()->add(
                        "targets.$index.color_id",
                        'Color can only be set when the target type is product.'
                    );
                }
            }
        });
    }
}
