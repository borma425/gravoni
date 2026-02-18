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
        // Convert comma-separated strings to arrays
        if ($this->has('available_sizes_input') && !empty($this->available_sizes_input)) {
            $sizes = array_map('trim', explode(',', $this->available_sizes_input));
            $this->merge(['available_sizes' => array_filter($sizes)]);
        }

        if ($this->has('available_colors_input') && !empty($this->available_colors_input)) {
            $colors = array_map('trim', explode(',', $this->available_colors_input));
            $this->merge(['available_colors' => array_filter($colors)]);
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
            'available_sizes_input' => 'nullable|string',
            'available_sizes' => 'nullable|array',
            'available_sizes.*' => 'string|max:50',
            'available_colors_input' => 'nullable|string',
            'available_colors' => 'nullable|array',
            'available_colors.*' => 'string|max:50',
            'sample' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }
}
