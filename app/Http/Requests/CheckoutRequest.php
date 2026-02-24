<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_address' => 'required|string',
            'customer_phone' => 'required|string|max:20',
            'governorate_id' => 'nullable|exists:governorates,id',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'الاسم مطلوب',
            'customer_address.required' => 'العنوان مطلوب',
            'customer_phone.required' => 'رقم الهاتف مطلوب',
        ];
    }
}
