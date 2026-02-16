@extends('layouts.app')

@section('title', 'تسجيل شراء')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">تسجيل شراء جديد</h1>
            <p class="mt-2 text-sm text-gray-600">تسجيل عملية شراء جديدة</p>
        </div>
        <a href="{{ route('purchases.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
            <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            العودة
        </a>
    </div>
</div>

<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <form action="{{ route('purchases.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="product_id" class="block text-sm font-medium text-gray-700">المنتج</label>
                <select name="product_id" id="product_id" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('product_id') border-red-300 @enderror">
                    <option value="">اختر منتج</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" 
                            data-average-cost="{{ $product->average_cost > 0 ? number_format($product->average_cost, 2) : '' }}"
                            {{ old('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }} ({{ $product->sku }})
                    </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700">الكمية</label>
                <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" required min="1"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('quantity') border-red-300 @enderror">
                @error('quantity')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="cost_price" class="block text-sm font-medium text-gray-700">
                    سعر التكلفة
                    <span class="text-gray-500 font-normal text-xs">(اختياري - سيتم استخدام متوسط التكلفة الحالي إذا لم يتم إدخاله)</span>
                </label>
                <input type="number" step="0.01" name="cost_price" id="cost_price" value="{{ old('cost_price') }}" min="0"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('cost_price') border-red-300 @enderror"
                       placeholder="اتركه فارغاً لاستخدام متوسط التكلفة الحالي">
                <p class="mt-1 text-xs text-gray-500">
                    <span id="average-cost-info" class="hidden">متوسط التكلفة الحالي: <strong class="text-slate-700" id="average-cost-value">0</strong> ج.م</span>
                    <span id="no-average-cost-info" class="hidden text-red-600">⚠️ يجب إدخال سعر التكلفة (المنتج جديد بدون مشتريات سابقة)</span>
                </p>
                @error('cost_price')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-reverse space-x-3">
                <a href="{{ route('purchases.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                    إلغاء
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-slate-700 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                    حفظ
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const costPriceInput = document.getElementById('cost_price');
    const averageCostInfo = document.getElementById('average-cost-info');
    const averageCostValue = document.getElementById('average-cost-value');
    const noAverageCostInfo = document.getElementById('no-average-cost-info');

    function updateAverageCostInfo() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const averageCost = selectedOption.getAttribute('data-average-cost');

        if (averageCost && averageCost !== '0' && averageCost !== '') {
            averageCostValue.textContent = averageCost;
            averageCostInfo.classList.remove('hidden');
            noAverageCostInfo.classList.add('hidden');
        } else {
            averageCostInfo.classList.add('hidden');
            noAverageCostInfo.classList.remove('hidden');
        }
    }

    productSelect.addEventListener('change', updateAverageCostInfo);
    
    // تحديث المعلومات عند تحميل الصفحة إذا كان هناك منتج محدد مسبقاً
    if (productSelect.value) {
        updateAverageCostInfo();
    }
});
</script>
@endsection
