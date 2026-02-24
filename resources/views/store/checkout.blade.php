@extends('layouts.store')

@section('title', 'إتمام الطلب - جرافوني')

@section('content')
<div class="bg-gray-50 min-h-screen py-8 md:py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl md:text-3xl font-bold text-slate-900 mb-8">إتمام الطلب</h1>

        <form action="{{ route('store.checkout.place') }}" method="POST" class="space-y-8">
            @csrf

            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 mb-4">معلومات التوصيل</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">الاسم الكامل *</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="w-full border border-gray-200 rounded-xl px-4 py-3">
                        @error('customer_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">رقم الهاتف *</label>
                        <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required class="w-full border border-gray-200 rounded-xl px-4 py-3">
                        @error('customer_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">عنوان التوصيل *</label>
                        <textarea name="customer_address" rows="3" required class="w-full border border-gray-200 rounded-xl px-4 py-3">{{ old('customer_address') }}</textarea>
                        @error('customer_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">رسوم التوصيل (ج.م)</label>
                        <select name="governorate_id" id="delivery-fees" class="w-full border border-gray-200 rounded-xl px-4 py-3">
                            <option value="">اختر المحافظة</option>
                            @foreach($governorates as $gov)
                            <option value="{{ $gov->id }}" data-fee="{{ $gov->shipping_fee }}">{{ $gov->name }} - {{ number_format($gov->shipping_fee, 0) }} ج.م</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 mb-4">ملخص الطلب</h2>
                @foreach($items as $item)
                <div class="flex justify-between py-2"><span>{{ $item['product']->name }} ({{ $item['quantity'] }}x)</span><span>{{ number_format($item['row_total'], 0) }} ج.م</span></div>
                @endforeach
                <div class="border-t mt-4 pt-4 flex justify-between"><span>رسوم التوصيل</span><span id="delivery-display">0 ج.م</span></div>
                <div class="border-t mt-2 pt-4 flex justify-between text-lg font-bold"><span>الإجمالي</span><span id="total-display">{{ number_format($subtotal, 0) }} ج.م</span></div>
                <input type="hidden" name="total_amount" id="total-amount" value="{{ $subtotal }}">
            </div>

            <div class="flex gap-4">
                <a href="{{ route('store.cart') }}" class="px-6 py-3 border-2 rounded-xl font-semibold text-center">العودة للسلة</a>
                <button type="submit" class="flex-1 bg-slate-900 text-white py-4 rounded-xl font-bold">تأكيد الطلب - الدفع عند الاستلام</button>
            </div>
        </form>
    </div>
</div>
<script>
document.getElementById('delivery-fees').addEventListener('change', function() {
    var opt = this.options[this.selectedIndex];
    var d = parseFloat(opt.dataset.fee || 0) || 0, s = {{ $subtotal }};
    document.getElementById('delivery-display').textContent = d.toFixed(0) + ' ج.م';
    document.getElementById('total-display').textContent = (s + d).toFixed(0) + ' ج.م';
    document.getElementById('total-amount').value = s + d;
});
</script>
@endsection
