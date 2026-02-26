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
            'customer_phone' => [
                'required',
                'string',
                'max:20',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $digits = preg_replace('/[^0-9]/', '', $value);
                    $digits = ltrim($digits, '0');
                    if (str_starts_with($digits, '20')) {
                        $digits = substr($digits, 2);
                    }
                    if (strlen($digits) < 10) {
                        $fail('رقم الجوال غير صالح. استخدم رقماً مصرياً من 11 رقماً (مثل 01xxxxxxxxx).');
                    }
                },
            ],
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
