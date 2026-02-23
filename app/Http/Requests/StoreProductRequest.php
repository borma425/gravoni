<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
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

            // Sizes + per-size color stock (no media here)
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

            // Separate colors section with media
            'available_colors' => 'nullable|array',
            'available_colors.*.color' => 'required_with:available_colors|string|max:50',
            'available_colors.*.images' => 'nullable|array',
            'available_colors.*.images.*' => 'string|max:500',
            'available_colors.*.videos' => 'nullable|array',
            'available_colors.*.videos.*' => 'string|max:500',
        ];
    }
}
