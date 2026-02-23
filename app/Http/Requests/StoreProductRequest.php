<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Independent colors are removed, replaced by nested colors inside availability
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product') ? $this->route('product')->id : null;

        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku,' . $productId,
            'selling_price' => 'required|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'available_sizes' => 'nullable|array',
            'available_sizes.*.size' => 'required_with:available_sizes|string|max:50',
            'available_sizes.*.chest_width_cm' => 'nullable|numeric|min:0',
            'available_sizes.*.weight_kg' => 'nullable|array',
            'available_sizes.*.weight_kg.min' => 'nullable|numeric|min:0',
            'available_sizes.*.weight_kg.max' => 'nullable|numeric|min:0',
            'available_sizes.*.height_cm' => 'nullable|array',
            'available_sizes.*.height_cm.min' => 'nullable|numeric|min:0',
            'available_sizes.*.height_cm.max' => 'nullable|numeric|min:0',
            'available_sizes.*.colors' => 'nullable|array',
            'available_sizes.*.colors.*.color' => 'required_with:available_sizes.*.colors|string|max:50',
            'available_sizes.*.colors.*.stock' => 'required_with:available_sizes.*.colors|integer|min:0',
            'available_sizes.*.colors.*.images' => 'nullable|array',
            'available_sizes.*.colors.*.images.*' => 'string|max:500',
            'available_sizes.*.colors.*.videos' => 'nullable|array',
            'available_sizes.*.colors.*.videos.*' => 'string|max:500',
        ];
    }
}
