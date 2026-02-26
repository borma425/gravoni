<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation: fill product_name from product_id when missing.
     */
    protected function prepareForValidation(): void
    {
        $items = $this->input('items', []);
        if (!is_array($items)) {
            return;
        }
        foreach ($items as $index => $item) {
            if (!is_array($item)) {
                continue;
            }
            $productName = $item['product_name'] ?? '';
            $productId = $item['product_id'] ?? null;
            if (($productName === '' || $productName === null) && $productId) {
                $product = Product::find($productId);
                if ($product) {
                    $items[$index]['product_name'] = $product->name;
                }
            }
        }
        $this->merge(['items' => $items]);

        // إعادة حساب total_amount من المنتجات + رسوم التوصيل لضمان صحته
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += ((float) ($item['price'] ?? 0)) * ((int) ($item['quantity'] ?? 0));
        }
        $deliveryFees = (float) ($this->input('delivery_fees', 0) ?? 0);
        $this->merge(['total_amount' => $subtotal + $deliveryFees]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_address' => 'required|string',
            'customer_numbers' => 'required|array|min:1',
            'customer_numbers.*' => 'string|max:20',
            'delivery_fees' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.product_id' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.size' => 'nullable|string|max:50',
            'items.*.color' => 'nullable|string|max:50',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,accepted,delivery_fees_paid,shipped,cancelled',
            'payment_method' => 'nullable|string',
            'governorate_id' => 'nullable|exists:governorates,id',
        ];
    }
}
