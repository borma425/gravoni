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
        if ($this->has('available_colors_input') && !empty($this->available_colors_input)) {
            $colors = array_map('trim', explode(',', $this->available_colors_input));
            $this->merge(['available_colors' => array_merge(
                $this->available_colors ?? [],
                array_filter($colors)
            )]);
        }
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
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'available_sizes' => 'nullable|array',
            'available_sizes.*.size' => 'required_with:available_sizes|string|max:50',
            'available_sizes.*.chest_width_cm' => 'required_with:available_sizes|numeric|min:0',
            'available_sizes.*.weight_kg' => 'required_with:available_sizes|array',
            'available_sizes.*.weight_kg.min' => 'required_with:available_sizes|numeric|min:0',
            'available_sizes.*.weight_kg.max' => 'required_with:available_sizes|numeric|min:0',
            'available_sizes.*.height_cm' => 'required_with:available_sizes|array',
            'available_sizes.*.height_cm.min' => 'required_with:available_sizes|numeric|min:0',
            'available_sizes.*.height_cm.max' => 'required_with:available_sizes|numeric|min:0',
            'available_colors_input' => 'nullable|string',
            'available_colors' => 'nullable|array',
            'available_colors.*' => 'string|max:50',
            'samples' => 'nullable|array',
            'samples.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'samples_remove' => 'nullable|array',
            'samples_remove.*' => 'integer',
        ];
    }
}
